import type { Metadata } from "next";
import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ShellHeader } from "@/app/_components/ShellHeader";
import { findProduct, formatPrice, products } from "@/lib/catalog";

type ProductPageProps = {
  params: Promise<{
    slug: string;
  }>;
};

export function generateStaticParams() {
  return products.map((product) => ({ slug: product.slug }));
}

export async function generateMetadata({
  params,
}: ProductPageProps): Promise<Metadata> {
  const { slug } = await params;
  const product = findProduct(slug);

  if (!product) {
    return {};
  }

  return {
    title: product.name,
    description: `${product.name} Karacabey Gross Market online sipariş.`,
    openGraph: {
      title: `${product.name} | Karacabey Gross Market`,
      description: product.description,
      images: [product.image],
    },
  };
}

export default async function ProductPage({ params }: ProductPageProps) {
  const { slug } = await params;
  const product = findProduct(slug);

  if (!product) {
    notFound();
  }

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "Product",
    name: product.name,
    brand: product.brand,
    image: product.image,
    description: product.description,
    offers: {
      "@type": "Offer",
      priceCurrency: "TRY",
      price: product.price,
      availability: product.stock > 0 ? "https://schema.org/InStock" : "https://schema.org/OutOfStock",
    },
  };

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
      />
      <ShellHeader />
      <main className="s44">
        <div className="s48">
          <Image
            src={product.image}
            alt={product.name}
            fill
            priority
            sizes="(max-width: 980px) 100vw, 45vw"
          />
        </div>
        <section className="s45">
          <span className="s21">{product.badge}</span>
          <h1>{product.name}</h1>
          <p>{product.description}</p>
          <div className="s46">
            <div className="s47">
              <strong>{formatPrice(product.price)}</strong>
              <span>{product.unit}</span>
            </div>
            {product.oldPrice ? (
              <div className="s47">
                <span>Eski fiyat</span>
                <s>{formatPrice(product.oldPrice)}</s>
              </div>
            ) : null}
            <div className="s47">
              <span>Stok</span>
              <strong>{product.stock} adet</strong>
            </div>
          </div>
          <div className="s10">
            <Link className="s11" href="/checkout">
              Sepete Ekle
            </Link>
            <Link className="s12" href="/products">
              Ürünlere Dön
            </Link>
          </div>
        </section>
      </main>
    </>
  );
}
