import Image from "next/image";
import Link from "next/link";
import { ShellHeader } from "@/app/_components/ShellHeader";
import { formatPrice, products } from "@/lib/catalog";

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
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
      />
      <ShellHeader />

      <main>
        <section className="s5">
          <div className="s6">
            <p className="s7">Karacabey online gross market</p>
            <h1 className="s8">Günlük market siparişleri, güvenli PayTR ödeme.</h1>
            <p className="s9">
              Yerel ürünler, gross fiyat avantajı, mobil uyumlu hızlı checkout ve güvenli ödeme akışı.
            </p>
            <div className="s10">
              <Link className="s11" href="/products">
                Alışverişe Başla
              </Link>
              <Link className="s12" href="/checkout">
                Checkout
              </Link>
            </div>
          </div>
          <div className="s13" aria-hidden="true">
            <Image
              src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&q=80"
              alt=""
              fill
              priority
              sizes="(max-width: 980px) 100vw, 52vw"
            />
          </div>
        </section>

        <section className="s14" aria-label="Öne çıkan ürünler">
          <div className="s15">
            <p className="s7">Bugünün seçimi</p>
            <h2 className="s16">Hızlı sepet ürünleri</h2>
          </div>
          <div className="s17">
            {products.map((product) => (
              <article className="s18" key={product.slug}>
                <Link href={`/product/${product.slug}`} className="s19">
                  <Image
                    src={product.image}
                    alt={product.name}
                    fill
                    sizes="(max-width: 620px) 100vw, (max-width: 980px) 50vw, 25vw"
                  />
                </Link>
                <div className="s20">
                  <span className="s21">{product.badge}</span>
                  <h3>{product.name}</h3>
                  <p>{product.brand}</p>
                  <div className="s22">
                    <strong>{formatPrice(product.price)}</strong>
                    <span>{product.unit}</span>
                  </div>
                  <Link className="s23" href="/checkout">
                    Sepete Ekle
                  </Link>
                </div>
              </article>
            ))}
          </div>
        </section>
      </main>
    </>
  );
}
