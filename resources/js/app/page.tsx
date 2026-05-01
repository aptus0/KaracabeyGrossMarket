import type { Metadata } from "next";
import { AdvertisingBanners } from "@/app/_components/AdvertisingBanners";
import { HomeCarousel } from "@/app/_components/HomeCarousel";
import { MobileCatalogRedirect } from "@/app/_components/MobileCatalogRedirect";
import { SeoHead } from "@/app/_components/SeoHead";
import { TrustBar } from "@/app/_components/TrustBar";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { ShowroomSection } from "@/app/_components/ShowroomSection";
import { BrandMarquee } from "@/app/_components/BrandMarquee";
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
    fetchFeaturedStorefrontProducts(12),
    fetchStorefrontCategories(),
  ]);

  // Vitrinler için ürünleri sanal olarak bölüyoruz (Gerçekte API'den özel endpointler ile çekilebilir)
  const weeklyProducts = featuredProducts.slice(0, 4);
  const bestSellers = featuredProducts.slice(4, 8);
  const cosmeticProducts = featuredProducts.slice(8, 12);

  return (
    <GuestLayout>
      <MobileCatalogRedirect />
      <SeoHead data={jsonLd} />

      <main className="min-h-screen bg-[#f3f6f8]">
        {/* Hero Slider */}
        <div className="relative">
          <HomeCarousel />
          {/* Subtle bottom glass overlay to blend with the background */}
          <div className="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-[#f3f6f8] to-transparent z-10 pointer-events-none" />
        </div>

        <TrustBar />

        <div className="max-w-[1120px] mx-auto px-4 md:px-6 relative z-20 -mt-6">
          {/* Showroom 1: Haftanın Ürünleri */}
          {weeklyProducts.length > 0 && (
            <ShowroomSection 
              title="Haftanın Fırsatları" 
              subtitle="Bu haftaya özel indirimli fiyatları kaçırmayın"
              products={weeklyProducts}
              theme="default"
              actionLink="/products?q=kampanya"
            />
          )}

          <AdvertisingBanners />

          {/* Showroom 2: Çok Satanlar */}
          {bestSellers.length > 0 && (
            <ShowroomSection 
              title="Çok Satan Ürünler" 
              subtitle="Müşterilerimizin en çok tercih ettiği ürünler"
              products={bestSellers}
              theme="bestseller"
              actionLink="/products?q=populer"
            />
          )}
        </div>

        {/* Markalar Kayan Yazı */}
        <BrandMarquee />

        <div className="max-w-[1120px] mx-auto px-4 md:px-6">
          {/* Showroom 3: Cilt & Kozmetik */}
          {cosmeticProducts.length > 0 && (
            <ShowroomSection 
              title="Cilt ve Kozmetik" 
              subtitle="Güzelliğinize değer katan seçkin markalar"
              products={cosmeticProducts}
              theme="cosmetics"
              actionLink="/categories/kozmetik"
            />
          )}
        </div>
      </main>
    </GuestLayout>
  );
}
