import { liteClient as algoliasearch } from "algoliasearch/lite";
import { formatPrice, products } from "@/lib/catalog";

export type ProductSuggestion = {
  id?: number | string;
  name: string;
  slug: string;
  brand?: string | null;
  price: string;
  image_url?: string | null;
  category?: string | null;
};

type SuggestResponse = {
  data?: ProductSuggestion[];
};

type AlgoliaProductHit = {
  objectID: string;
  name?: string;
  slug?: string;
  brand?: string | null;
  price?: string;
  price_cents?: number;
  image_url?: string | null;
  category?: string | null;
};

const apiBaseUrl = process.env.NEXT_PUBLIC_API_URL ?? "";
const algoliaAppId = process.env.NEXT_PUBLIC_ALGOLIA_APP_ID;
const algoliaSearchKey = process.env.NEXT_PUBLIC_ALGOLIA_SEARCH_KEY;
const algoliaProductsIndex = process.env.NEXT_PUBLIC_ALGOLIA_PRODUCTS_INDEX;

export async function fetchProductSuggestions(query: string, signal?: AbortSignal): Promise<ProductSuggestion[]> {
  const normalizedQuery = query.trim();

  if (normalizedQuery.length < 2) {
    return [];
  }

  if (algoliaAppId && algoliaSearchKey && algoliaProductsIndex) {
    return fetchAlgoliaSuggestions(normalizedQuery);
  }

  const params = new URLSearchParams({ q: normalizedQuery });
  const response = await fetch(`${apiBaseUrl}/api/v1/products/suggest?${params.toString()}`, {
    headers: {
      Accept: "application/json",
    },
    signal,
  });

  if (!response.ok) {
    throw new Error("Search failed.");
  }

  const payload = (await response.json()) as SuggestResponse;

  return payload.data?.slice(0, 6) ?? [];
}

export function localProductSuggestions(query: string): ProductSuggestion[] {
  const normalizedQuery = query.toLocaleLowerCase("tr-TR").trim();

  if (normalizedQuery.length < 2) {
    return [];
  }

  return products
    .filter((product) =>
      [product.name, product.brand, product.category, product.description].some((value) =>
        value.toLocaleLowerCase("tr-TR").includes(normalizedQuery),
      ),
    )
    .slice(0, 6)
    .map((product, index) => ({
      id: index + 1,
      name: product.name,
      slug: product.slug,
      brand: product.brand,
      price: formatPrice(product.price),
      image_url: product.image,
      category: product.category,
    }));
}

async function fetchAlgoliaSuggestions(query: string): Promise<ProductSuggestion[]> {
  const client = algoliasearch(algoliaAppId!, algoliaSearchKey!);
  const response = await client.searchForHits<AlgoliaProductHit>({
    requests: [
      {
        indexName: algoliaProductsIndex!,
        query,
        hitsPerPage: 6,
      },
    ],
  });
  const result = response.results[0];

  return result.hits
    .filter((hit) => hit.name && hit.slug)
    .map((hit) => ({
      id: hit.objectID,
      name: hit.name!,
      slug: hit.slug!,
      brand: hit.brand,
      price: hit.price ?? (typeof hit.price_cents === "number" ? formatPrice(hit.price_cents / 100) : ""),
      image_url: hit.image_url,
      category: hit.category,
    }));
}
