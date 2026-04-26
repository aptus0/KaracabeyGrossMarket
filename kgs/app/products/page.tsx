import type { Metadata } from "next";
import Image from "next/image";
import Link from "next/link";
import { ShellHeader } from "@/app/_components/ShellHeader";
import { formatPrice, products } from "@/lib/catalog";

export const metadata: Metadata = {
  title: "Ürünler",
  description: "Karacabey Gross Market ürün kataloğu ve online alışveriş.",
};

export default function ProductsPage() {
  return (
    <>
      <ShellHeader />
      <main className="s34">
        <div className="s15">
          <div>
            <p className="s7">Katalog</p>
            <h1 className="s16">Karacabey Gross ürünleri</h1>
          </div>
          <Link className="s12" href="/checkout">
            Sepete Git
          </Link>
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
      </main>
    </>
  );
}
