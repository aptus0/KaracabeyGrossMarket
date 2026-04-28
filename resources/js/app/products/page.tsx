import type { Metadata } from "next";
import Link from "next/link";
import { SlidersHorizontal } from "lucide-react";
import { CategoryCard } from "@/app/_components/CategoryCard";
import { ProductGrid } from "@/app/_components/ProductGrid";
import { SearchBar } from "@/app/_components/SearchBar";
import { SeoHead } from "@/app/_components/SeoHead";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { buildMetadata, siteUrl } from "@/lib/seo";
import {
  fetchStorefrontCategories,
  fetchStorefrontProducts,
} from "@/lib/storefront-products";

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

  const [{ products: selectedProducts, total }, categories] = await Promise.all([
    fetchStorefrontProducts({
      category: params.category,
      query: params.q,
      perPage: 48,
    }),
    fetchStorefrontCategories(),
  ]);

  const activeCategory = categories.find((c) => c.slug === params.category);

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

        {/* ── Search / hero bar ─────────────────────────── */}
        <section className="catalog-hero">
          <div>
            <p className="eyebrow">Katalog</p>
            <h1>
              {activeCategory ? activeCategory.name : "Tüm Ürünler"}
            </h1>
          </div>
          <SearchBar />
        </section>

        {/* ── Category chips ────────────────────────────── */}
        {categories.length > 0 && (
          <div className="catalog-chips">
            <Link
              href="/products"
              className={`catalog-chip${!params.category ? " catalog-chip--active" : ""}`}
            >
              Tümü
            </Link>
            {categories.map((cat) => (
              <Link
                key={cat.slug}
                href={`/products?category=${cat.slug}`}
                className={`catalog-chip${params.category === cat.slug ? " catalog-chip--active" : ""}`}
              >
                {cat.name}
              </Link>
            ))}
          </div>
        )}

        {/* ── Compact category grid (desktop sidebar feel) ── */}
        {categories.length > 0 && (
          <div className="category-grid category-grid--compact">
            {categories.map((category) => (
              <CategoryCard key={category.slug} category={category} />
            ))}
          </div>
        )}

        {/* ── Toolbar ───────────────────────────────────── */}
        <div className="catalog-toolbar">
          <span className="catalog-toolbar__count">
            <SlidersHorizontal size={15} />
            {total} ürün
          </span>
          <div className="catalog-toolbar__actions">
            {params.category && (
              <Link className="catalog-chip" href="/products">
                Filtreyi Kaldır
              </Link>
            )}
            <Link className="secondary-action" href="/checkout">
              Sepete Git
            </Link>
          </div>
        </div>

        {/* ── Product grid ──────────────────────────────── */}
        {selectedProducts.length === 0 ? (
          <div className="catalog-empty">
            <p>Bu kategoride ürün bulunamadı.</p>
            <Link className="primary-action" href="/products">
              Tüm ürünleri gör
            </Link>
          </div>
        ) : (
          <ProductGrid products={selectedProducts} />
        )}

      </main>
    </GuestLayout>
  );
}
