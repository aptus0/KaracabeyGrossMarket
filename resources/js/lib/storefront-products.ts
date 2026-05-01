import "server-only";

import {
  filterProducts as fallbackFilterProducts,
  findProduct as fallbackFindProduct,
  type KgmCategory,
  type KgmProduct,
} from "@/lib/catalog";
import { resolveInternalApiOrigin } from "@/lib/server-config";

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

type CategoryIndexResponse = {
  data?: Array<{
    id: number;
    name: string;
    slug: string;
    description?: string | null;
    image_url?: string | null;
    product_count?: number;
    children?: Array<{
      id: number;
      name: string;
      slug: string;
      description?: string | null;
      image_url?: string | null;
      product_count?: number;
    }>;
  }>;
};

function stripTrailingSlash(value: string | null | undefined) {
  return value ? value.replace(/\/+$/, "") : "";
}

function buildServerApiUrl(path: string) {
  const origin = stripTrailingSlash(resolveInternalApiOrigin());

  return `${origin}${path.startsWith("/") ? path : `/${path}`}`;
}

function toStorefrontProduct(product: StorefrontProductResponse): KgmProduct {
  const primaryCategory = product.categories?.[0];
  const imageUrl = safeImageUrl(product.image_url) ?? "/assets/kgm-logo.png";
  const sku = getSeoString(product.seo, "erkur_kod") ?? getSeoString(product.seo, "sku");
  const hasCompareAtPrice = Boolean(
    product.compare_at_price_cents && product.compare_at_price_cents > product.price_cents,
  );

  return {
    slug: product.slug,
    name: product.name,
    brand: product.brand?.trim() || "Karacabey Gross Market",
    sku,
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
    categoryName: primaryCategory?.name ?? "Genel katalog",
  };
}

function getSeoString(seo: StorefrontProductResponse["seo"], key: string): string | undefined {
  const value = seo?.[key];

  return typeof value === "string" && value.trim() ? value.trim() : undefined;
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

function toStorefrontCategory(category: NonNullable<CategoryIndexResponse["data"]>[number]): KgmCategory {
  return {
    slug: category.slug,
    name: category.name,
    count: typeof category.product_count === "number" ? category.product_count : undefined,
    description: category.description ?? null,
    imageUrl: category.image_url ?? null,
    children: (category.children ?? []).map((child) => ({
      slug: child.slug,
      name: child.name,
      count: typeof child.product_count === "number" ? child.product_count : undefined,
      description: child.description ?? null,
      imageUrl: child.image_url ?? null,
    })),
  };
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

    return (payload.data ?? []).map(toStorefrontCategory);
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
