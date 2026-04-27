import type { Metadata } from "next";
import Link from "next/link";
import { CategoryCard } from "@/app/_components/CategoryCard";
import { ProductGrid } from "@/app/_components/ProductGrid";
import { SearchBar } from "@/app/_components/SearchBar";
import { SeoHead } from "@/app/_components/SeoHead";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { categories } from "@/lib/catalog";
import { buildMetadata, siteUrl } from "@/lib/seo";
import { fetchStorefrontProducts } from "@/lib/storefront-products";

export const dynamic = "force-dynamic";

export const metadata: Metadata = buildMetadata({
  title: "Ürünler",
  description: "Karacabey Gross Market ürün kataloğu, kategori filtreleri ve hızlı online alışveriş akışı.",
  path: "/products",
  keywords: ["ürünler", "ürün kataloğu", "market kategorileri", "online alışveriş"],
});

type ProductsPageProps = {
  searchParams: Promise<{
    category?: string;
    q?: string;
  }>;
};

export default async function ProductsPage({ searchParams }: ProductsPageProps) {
  const params = await searchParams;
  const { products: selectedProducts, total } = await fetchStorefrontProducts({
    category: params.category,
    query: params.q,
    perPage: 24,
  });
  const itemListSchema = {
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    name: "Karacabey Gross Market Ürünler",
    description: "Karacabey Gross Market ürün kataloğu ve hızlı online sipariş akışı.",
    url: `${siteUrl}/products`,
    mainEntity: {
      "@type": "ItemList",
      numberOfItems: selectedProducts.length,
      itemListElement: selectedProducts.map((product, index) => ({
        "@type": "ListItem",
        position: index + 1,
        url: `${siteUrl}/product/${product.slug}`,
        name: product.name,
      })),
    },
  };

  return (
    <GuestLayout>
      <SeoHead data={itemListSchema} />
      <main className="catalog-page">
        <section className="catalog-hero">
          <div>
            <p className="eyebrow">Katalog</p>
            <h1>Karacabey Gross ürünleri</h1>
          </div>
          <SearchBar />
        </section>

        <div className="category-grid category-grid--compact">
          {categories.map((category) => (
            <CategoryCard key={category.slug} category={category} />
          ))}
        </div>

        <div className="catalog-toolbar">
          <span>{total} ürün</span>
          <Link className="secondary-action" href="/checkout">
            Sepete Git
          </Link>
        </div>

        <ProductGrid products={selectedProducts} />
      </main>
    </GuestLayout>
  );
}
