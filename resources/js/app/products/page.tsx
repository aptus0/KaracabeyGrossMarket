import type { Metadata } from "next";
import Link from "next/link";
import { SlidersHorizontal } from "lucide-react";
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
    page?: string;
    q?: string;
  }>;
};

export default async function ProductsPage({ searchParams }: ProductsPageProps) {
  const params = await searchParams;
  const currentPage = Math.max(1, Number.parseInt(params.page ?? "1", 10) || 1);

  const [{
    products: selectedProducts,
    total,
    currentPage: resolvedPage,
    lastPage,
    from,
    to,
  }, categories] = await Promise.all([
    fetchStorefrontProducts({
      category: params.category,
      page: currentPage,
      query: params.q,
      perPage: 48,
    }),
    fetchStorefrontCategories(),
  ]);

  const activeCategory = categories
    .flatMap((category) => [category, ...(category.children ?? [])])
    .find((category) => category.slug === params.category);
  const pageWindowStart = Math.max(1, resolvedPage - 2);
  const pageWindowEnd = Math.min(lastPage, resolvedPage + 2);
  const visiblePages = Array.from(
    { length: Math.max(pageWindowEnd - pageWindowStart + 1, 0) },
    (_, index) => pageWindowStart + index,
  );

  const buildProductsHref = (page: number) => {
    const nextParams = new URLSearchParams();

    if (params.category) {
      nextParams.set("category", params.category);
    }

    if (params.q) {
      nextParams.set("q", params.q);
    }

    if (page > 1) {
      nextParams.set("page", String(page));
    }

    const queryString = nextParams.toString();

    return queryString ? `/products?${queryString}` : "/products";
  };

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
          <section className="catalog-filter-panel" aria-label="Kategori filtreleri">
            <div className="catalog-filter-panel__head">
              <div>
                <span className="catalog-filter-panel__label">Kategoriler</span>
                <strong>{activeCategory ? activeCategory.name : "Tüm reyonlar"}</strong>
              </div>
              <span className="catalog-filter-panel__meta">{categories.length} kategori</span>
            </div>
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
          </section>
        )}

        {/* ── Toolbar ───────────────────────────────────── */}
        <div className="catalog-toolbar">
          <span
            className="catalog-toolbar__count"
            data-nosnippet
            translate="no"
          >
            <SlidersHorizontal size={15} />
            {from > 0 && to > 0 ? (
              <>
                <span aria-hidden="true">{from}</span>
                <span aria-hidden="true">–</span>
                <span aria-hidden="true">{to}</span>
                <span aria-hidden="true"> / </span>
                <span aria-hidden="true">{total}</span>
              </>
            ) : (
              <span aria-hidden="true">{total}</span>
            )}{" "}
            ürün
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

        {lastPage > 1 && (
          <nav className="catalog-pagination" aria-label="Ürün sayfalama">
            <Link
              href={buildProductsHref(Math.max(resolvedPage - 1, 1))}
              aria-disabled={resolvedPage <= 1}
              className={`catalog-chip${resolvedPage <= 1 ? " catalog-chip--disabled" : ""}`}
            >
              Önceki
            </Link>

            {pageWindowStart > 1 && (
              <>
                <Link href={buildProductsHref(1)} className="catalog-chip">
                  1
                </Link>
                {pageWindowStart > 2 && <span className="catalog-pagination__ellipsis">…</span>}
              </>
            )}

            {visiblePages.map((page) => (
              <Link
                key={page}
                href={buildProductsHref(page)}
                className={`catalog-chip${page === resolvedPage ? " catalog-chip--active" : ""}`}
              >
                {page}
              </Link>
            ))}

            {pageWindowEnd < lastPage && (
              <>
                {pageWindowEnd < lastPage - 1 && <span className="catalog-pagination__ellipsis">…</span>}
                <Link href={buildProductsHref(lastPage)} className="catalog-chip">
                  {lastPage}
                </Link>
              </>
            )}

            <Link
              href={buildProductsHref(Math.min(resolvedPage + 1, lastPage))}
              aria-disabled={resolvedPage >= lastPage}
              className={`catalog-chip${resolvedPage >= lastPage ? " catalog-chip--disabled" : ""}`}
            >
              Sonraki
            </Link>
          </nav>
        )}

      </main>
    </GuestLayout>
  );
}
