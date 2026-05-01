import type { Metadata } from "next";
import { MobileCatalogRedirect } from "@/app/_components/MobileCatalogRedirect";
import { SeoHead } from "@/app/_components/SeoHead";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { ShowroomSection } from "@/app/_components/ShowroomSection";
import { TrustBar } from "@/app/_components/TrustBar";
import { CategoryNavigation } from "@/app/_components/CategoryNavigation";
import { PromoCampaignCards } from "@/app/_components/PromoCampaignCards";
import { HeroSlider } from "@/app/_components/HeroSlider";
import { buildMetadata, siteUrl } from "@/lib/seo";
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
  url: siteUrl,
  areaServed: "Karacabey",
  paymentAccepted: "Credit Card, Debit Card",
};

export default async function Home() {
  const [featuredProducts, categories] = await Promise.all([
    fetchFeaturedStorefrontProducts(12),
    fetchStorefrontCategories(),
  ]);

  const weeklyProducts = featuredProducts.slice(0, 5);

  return (
    <GuestLayout>
      <MobileCatalogRedirect />
      <SeoHead data={jsonLd} />

      <main className="min-h-screen bg-white">
        {/* Hero Slider - Geniş ve Prominent */}
        <HeroSlider />

        {/* Category Navigation Grid */}
        <CategoryNavigation categories={categories} />

        {/* Öne Çıkan Ürünler Section */}
        {weeklyProducts.length > 0 && (
          <div className="max-w-[1120px] mx-auto px-4 md:px-6 py-12">
            <ShowroomSection
              title="Öne Çıkan Ürünler"
              subtitle=""
              products={weeklyProducts}
              theme="default"
              actionLink="/products"
              actionText="Tümünü Gör"
            />
          </div>
        )}

        {/* Promo Campaign Cards - 3 kartlı kampanya bölümü */}
        <PromoCampaignCards />

        {/* Trust Bar */}
        <div className="bg-gray-50 py-12">
          <TrustBar />
        </div>
      </main>
    </GuestLayout>
  );
}
