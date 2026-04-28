import "server-only";

import { existsSync, readFileSync } from "node:fs";
import { resolve } from "node:path";
import {
  filterProducts as fallbackFilterProducts,
  findProduct as fallbackFindProduct,
  type KgmCategory,
  type KgmProduct,
} from "@/lib/catalog";

type StorefrontCategory = {
  id: number;
  name: string;
  slug: string;
};

type StorefrontProductResponse = {
  id: number;
  name: string;
  slug: string;
  description?: string | null;
  brand?: string | null;
  price_cents: number;
  price: string;
  compare_at_price_cents?: number | null;
  stock_quantity: number;
  image_url?: string | null;
  seo?: Record<string, unknown> | null;
  categories?: StorefrontCategory[];
};

type ProductIndexResponse = {
  data?: StorefrontProductResponse[];
  total?: number;
  per_page?: number;
  current_page?: number;
  last_page?: number;
  from?: number | null;
  to?: number | null;
};

function readLaravelEnvValue(key: string) {
  const envPath = resolve(process.cwd(), "..", "..", ".env");

  if (!existsSync(envPath)) {
    return null;
  }

  const file = readFileSync(envPath, "utf8");

  for (const rawLine of file.split(/\r?\n/)) {
    const line = rawLine.trim();

    if (!line || line.startsWith("#") || !line.startsWith(`${key}=`)) {
      continue;
    }

    const value = line.slice(key.length + 1).trim();

    return value.replace(/^['"]|['"]$/g, "");
  }

  return null;
}

function stripTrailingSlash(value: string | null | undefined) {
  return value ? value.replace(/\/+$/, "") : "";
}

function normalizeBackendOrigin(value: string | null | undefined) {
  const origin = stripTrailingSlash(value);

  if (!origin) {
    return "";
  }

  try {
    const parsed = new URL(origin);

    if (parsed.protocol === "http:" && parsed.hostname.endsWith(".test")) {
      parsed.protocol = "https:";
    }

    return stripTrailingSlash(parsed.toString());
  } catch {
    return origin;
  }
}

function isLocalTestOrigin(value: string | null | undefined) {
  if (!value) {
    return false;
  }

  try {
    return new URL(value).hostname.endsWith(".test");
  } catch {
    return false;
  }
}

function resolveBackendOrigin() {
  const explicitApiOrigin = process.env.NEXT_PUBLIC_API_URL
    ?? process.env.API_URL
    ?? readLaravelEnvValue("API_URL");

  if (explicitApiOrigin) {
    return normalizeBackendOrigin(explicitApiOrigin);
  }

  const appOrigin = process.env.APP_URL
    ?? readLaravelEnvValue("APP_URL");

  if (isLocalTestOrigin(appOrigin)) {
    return "http://127.0.0.1:8000";
  }

  return normalizeBackendOrigin(appOrigin ?? "http://127.0.0.1:8000");
}

function buildServerApiUrl(path: string) {
  return `${resolveBackendOrigin()}${path.startsWith("/") ? path : `/${path}`}`;
}

function toStorefrontProduct(product: StorefrontProductResponse): KgmProduct {
  const primaryCategory = product.categories?.[0];
  const imageUrl = safeImageUrl(product.image_url) ?? "/assets/kgm-logo.png";
  const hasCompareAtPrice = Boolean(
    product.compare_at_price_cents && product.compare_at_price_cents > product.price_cents,
  );

  return {
    slug: product.slug,
    name: product.name,
    brand: product.brand?.trim() || "Karacabey Gross Market",
    price: product.price_cents / 100,
    oldPrice: hasCompareAtPrice ? product.compare_at_price_cents! / 100 : undefined,
    unit: "adet",
    stock: product.stock_quantity,
    image: imageUrl,
    gallery: [imageUrl],
    badge: hasCompareAtPrice
      ? "Avantaj"
      : product.stock_quantity > 0
        ? primaryCategory?.name ?? "Stokta"
        : "Tükendi",
    description: product.description?.trim() || `${product.name} için güncel ürün bilgisi.`,
    category: primaryCategory?.slug ?? "genel",
  };
}

function safeImageUrl(url?: string | null): string | null {
  if (!url) {
    return null;
  }

  try {
    const parsedUrl = new URL(url);

    return parsedUrl.protocol === "https:" || parsedUrl.protocol === "http:" ? url : null;
  } catch {
    return null;
  }
}

export async function fetchStorefrontProducts(options?: {
  category?: string;
  query?: string;
  perPage?: number;
  page?: number;
}) {
  const category = options?.category?.trim();
  const query = options?.query?.trim();
  const perPage = options?.perPage ?? 12;
  const page = options?.page && options.page > 0 ? Math.floor(options.page) : 1;
  const params = new URLSearchParams();

  if (category) {
    params.set("category", category);
  }

  if (query) {
    params.set("q", query);
  }

  params.set("per_page", String(perPage));
  params.set("page", String(page));

  try {
    const response = await fetch(buildServerApiUrl(`/api/v1/products?${params.toString()}`), {
      headers: {
        Accept: "application/json",
      },
      cache: "no-store",
    });

    if (!response.ok) {
      throw new Error(`Products request failed with ${response.status}.`);
    }

    const payload = (await response.json()) as ProductIndexResponse;
    const products = (payload.data ?? []).map(toStorefrontProduct);

    return {
      products,
      total: typeof payload.total === "number" ? payload.total : products.length,
      perPage: typeof payload.per_page === "number" ? payload.per_page : perPage,
      currentPage: typeof payload.current_page === "number" ? payload.current_page : page,
      lastPage: typeof payload.last_page === "number" ? payload.last_page : 1,
      from: typeof payload.from === "number" ? payload.from : products.length > 0 ? (page - 1) * perPage + 1 : 0,
      to: typeof payload.to === "number" ? payload.to : (page - 1) * perPage + products.length,
    };
  } catch {
    const fallbackProducts = fallbackFilterProducts(category, query);
    const total = fallbackProducts.length;
    const start = (page - 1) * perPage;
    const visibleProducts = fallbackProducts.slice(start, start + perPage);

    return {
      products: visibleProducts,
      total,
      perPage,
      currentPage: page,
      lastPage: Math.max(Math.ceil(total / perPage), 1),
      from: visibleProducts.length > 0 ? start + 1 : 0,
      to: visibleProducts.length > 0 ? start + visibleProducts.length : 0,
    };
  }
}

type CategoryIndexResponse = {
  data?: Array<{
    id: number;
    name: string;
    slug: string;
    description?: string | null;
    image_url?: string | null;
  }>;
};

export async function fetchStorefrontCategories(): Promise<KgmCategory[]> {
  try {
    const response = await fetch(buildServerApiUrl("/api/v1/categories"), {
      headers: { Accept: "application/json" },
      cache: "no-store",
    });

    if (!response.ok) {
      throw new Error(`Categories request failed with ${response.status}.`);
    }

    const payload = (await response.json()) as CategoryIndexResponse;

    return (payload.data ?? []).map((c) => ({ slug: c.slug, name: c.name }));
  } catch {
    return [];
  }
}

export async function fetchFeaturedStorefrontProducts(limit = 8) {
  const { products } = await fetchStorefrontProducts({ perPage: limit });

  return products;
}

export async function fetchStorefrontProduct(slug: string) {
  try {
    const response = await fetch(buildServerApiUrl(`/api/v1/products/${encodeURIComponent(slug)}`), {
      headers: {
        Accept: "application/json",
      },
      cache: "no-store",
    });

    if (!response.ok) {
      throw new Error(`Product request failed with ${response.status}.`);
    }

    const payload = (await response.json()) as { data?: StorefrontProductResponse };

    return payload.data ? toStorefrontProduct(payload.data) : null;
  } catch {
    return fallbackFindProduct(slug) ?? null;
  }
}
