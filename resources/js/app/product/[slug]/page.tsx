import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { Breadcrumb } from "@/app/_components/Breadcrumb";
import { PriceBox } from "@/app/_components/PriceBox";
import { ProductGallery } from "@/app/_components/ProductGallery";
import { ProductPurchasePanel } from "@/app/_components/ProductPurchasePanel";
import { ProductSlider } from "@/app/_components/ProductSlider";
import { SeoHead } from "@/app/_components/SeoHead";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { findProduct, products } from "@/lib/catalog";

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
    <GuestLayout>
      <SeoHead data={jsonLd} />
      <main className="product-page">
        <Breadcrumb
          items={[
            { href: "/", label: "Ana Sayfa" },
            { href: "/products", label: "Ürünler" },
            { label: product.name },
          ]}
        />

        <section className="product-detail">
          <ProductGallery images={product.gallery ?? [product.image]} name={product.name} />
          <div className="product-detail__content">
            <span className="pill">{product.badge}</span>
            <h1>{product.name}</h1>
            <p>{product.description}</p>
            <PriceBox price={product.price} oldPrice={product.oldPrice} unit={product.unit} />
            <div className="stock-row">
              <span>Stok</span>
              <strong>{product.stock} adet</strong>
            </div>
            <ProductPurchasePanel productSlug={product.slug} />
            <Link className="secondary-action" href="/products">
              Ürünlere Dön
            </Link>
          </div>
        </section>

        <section className="content-band" aria-label="Benzer ürünler">
          <div className="section-heading">
            <p className="eyebrow">Benzer ürünler</p>
            <h2>Sepete yakışanlar</h2>
          </div>
          <ProductSlider products={products.filter((item) => item.slug !== product.slug)} />
        </section>
      </main>
    </GuestLayout>
  );
}
