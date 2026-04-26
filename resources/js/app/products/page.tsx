import type { Metadata } from "next";
import Link from "next/link";
import { CategoryCard } from "@/app/_components/CategoryCard";
import { ProductGrid } from "@/app/_components/ProductGrid";
import { SearchBar } from "@/app/_components/SearchBar";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { categories, filterProducts } from "@/lib/catalog";

export const metadata: Metadata = {
  title: "Ürünler",
  description: "Karacabey Gross Market ürün kataloğu ve online alışveriş.",
};

type ProductsPageProps = {
  searchParams: Promise<{
    category?: string;
    q?: string;
  }>;
};

export default async function ProductsPage({ searchParams }: ProductsPageProps) {
  const params = await searchParams;
  const selectedProducts = filterProducts(params.category, params.q);

  return (
    <GuestLayout>
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
          <span>{selectedProducts.length} ürün</span>
          <Link className="secondary-action" href="/checkout">
            Sepete Git
          </Link>
        </div>

        <ProductGrid products={selectedProducts} />
      </main>
    </GuestLayout>
  );
}
