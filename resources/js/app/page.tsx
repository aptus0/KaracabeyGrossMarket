import { CampaignBanner } from "@/app/_components/CampaignBanner";
import { CategoryCard } from "@/app/_components/CategoryCard";
import { HomeCarousel } from "@/app/_components/HomeCarousel";
import { PageBuilderBlock } from "@/app/_components/PageBuilderBlock";
import { ProductSlider } from "@/app/_components/ProductSlider";
import { SeoHead } from "@/app/_components/SeoHead";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { categories, products } from "@/lib/catalog";

const jsonLd = {
  "@context": "https://schema.org",
  "@type": "GroceryStore",
  name: "Karacabey Gross Market",
  url: "https://karacabeygrossmarket.com",
  areaServed: "Karacabey",
  paymentAccepted: "Credit Card, Debit Card",
};

export default function Home() {
  return (
    <GuestLayout>
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
          <ProductSlider products={products} />
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
