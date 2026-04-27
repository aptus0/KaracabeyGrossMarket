import type { Metadata } from "next";
import { CampaignBanner } from "@/app/_components/CampaignBanner";
import { CategoryCard } from "@/app/_components/CategoryCard";
import { HomeCarousel } from "@/app/_components/HomeCarousel";
import { MobileCatalogRedirect } from "@/app/_components/MobileCatalogRedirect";
import { PageBuilderBlock } from "@/app/_components/PageBuilderBlock";
import { ProductSlider } from "@/app/_components/ProductSlider";
import { SeoHead } from "@/app/_components/SeoHead";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { categories } from "@/lib/catalog";
import { buildMetadata } from "@/lib/seo";
import { fetchFeaturedStorefrontProducts } from "@/lib/storefront-products";

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
  const featuredProducts = await fetchFeaturedStorefrontProducts(8);

  return (
    <GuestLayout>
      <MobileCatalogRedirect />
      <SeoHead data={jsonLd} />

      <main>
        <HomeCarousel />

        <section className="content-band" aria-label="Kategoriler">
          <div className="section-heading">
            <p className="eyebrow">Kategoriler</p>
            <h2>Hızlı alışveriş alanları</h2>
          </div>
          <div className="category-grid">
            {categories.map((category) => (
              <CategoryCard key={category.slug} category={category} />
            ))}
          </div>
        </section>

        <CampaignBanner
          title="Haftalık gross fırsatları"
          description="Temel gıda ve günlük ürünlerde avantajlı sepetler."
          href="/products"
        />

        <section className="content-band" aria-label="Öne çıkan ürünler">
          <div className="section-heading">
            <p className="eyebrow">Bugünün seçimi</p>
            <h2>Hızlı sepet ürünleri</h2>
          </div>
          <ProductSlider products={featuredProducts} />
        </section>

        <PageBuilderBlock eyebrow="Operasyon" title="Karacabey içinde düzenli teslimat">
          <p>
            Sipariş, ödeme, iade ve teslimat akışı Laravel API üzerinden yönetilir; müşteri vitrini NextJS ile hızlı
            ve SEO odaklı çalışır.
          </p>
        </PageBuilderBlock>
      </main>
    </GuestLayout>
  );
}
