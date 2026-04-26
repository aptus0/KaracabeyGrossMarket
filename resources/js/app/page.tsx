import Image from "next/image";
import Link from "next/link";
import { CampaignBanner } from "@/app/_components/CampaignBanner";
import { CategoryCard } from "@/app/_components/CategoryCard";
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
  paymentAccepted: "PayTR, Credit Card, Debit Card",
};

export default function Home() {
  return (
    <GuestLayout>
      <SeoHead data={jsonLd} />

      <main>
        <section className="hero-section">
          <div className="hero-section__copy">
            <p className="eyebrow">Karacabey online gross market</p>
            <h1>Günlük market siparişleri, güvenli PayTR ödeme.</h1>
            <p>
              Yerel ürünler, gross fiyat avantajı, mobil uyumlu hızlı checkout ve güvenli ödeme akışı.
            </p>
            <div className="hero-section__actions">
              <Link className="primary-action" href="/products">
                Alışverişe Başla
              </Link>
              <Link className="secondary-action" href="/checkout">
                Checkout
              </Link>
            </div>
          </div>
          <div className="hero-section__image" aria-hidden="true">
            <Image
              src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&q=80"
              alt=""
              fill
              priority
              sizes="(max-width: 980px) 100vw, 52vw"
            />
          </div>
        </section>

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
