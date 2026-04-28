import type { Metadata } from "next";
import { AdvertisingBanners } from "@/app/_components/AdvertisingBanners";
import { CategoryCard } from "@/app/_components/CategoryCard";
import { HomeCarousel } from "@/app/_components/HomeCarousel";
import { MobileCatalogRedirect } from "@/app/_components/MobileCatalogRedirect";
import { ProductSlider } from "@/app/_components/ProductSlider";
import { SeoHead } from "@/app/_components/SeoHead";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { buildMetadata } from "@/lib/seo";
import {
  fetchFeaturedStorefrontProducts,
  fetchStorefrontCategories,
} from "@/lib/storefront-products";

export const dynamic = "force-dynamic";
export const metadata: Metadata = buildMetadata({
  title: "Ana Sayfa",
  description: "Karacabey Gross Market ana sayfası: hızlı teslimat, güvenli ödeme, kampanyalar ve öne çıkan ürünler.",
  path: "/",
  keywords: ["ana sayfa", "kampanyalar", "öne çıkan ürünler", "günlük market alışverişi"],
});

const jsonLd = {
  "@context": "https://schema.org",
  "@type": "GroceryStore",
  name: "Karacabey Gross Market",
  url: "https://karacabeygrossmarket.com",
  areaServed: "Karacabey",
  paymentAccepted: "Credit Card, Debit Card",
};

export default async function Home() {
  const [featuredProducts, categories] = await Promise.all([
    fetchFeaturedStorefrontProducts(8),
    fetchStorefrontCategories(),
  ]);

  return (
    <GuestLayout>
      <MobileCatalogRedirect />
      <SeoHead data={jsonLd} />

      <main>
        <HomeCarousel />

        {categories.length > 0 && (
          <section className="content-band" aria-label="Kategoriler">
            <div className="section-heading">
              <div>
                <p className="eyebrow">Kategoriler</p>
                <h2>Hızlı alışveriş alanları</h2>
              </div>
            </div>
            <div className="category-grid">
              {categories.map((category) => (
                <CategoryCard key={category.slug} category={category} />
              ))}
            </div>
          </section>
        )}

        <AdvertisingBanners />

        <section className="content-band" aria-label="Öne çıkan ürünler">
          <div className="section-heading">
            <div>
              <p className="eyebrow">Bugünün seçimi</p>
              <h2>Hızlı sepet ürünleri</h2>
            </div>
          </div>
          <ProductSlider products={featuredProducts} />
        </section>
      </main>
    </GuestLayout>
  );
}
