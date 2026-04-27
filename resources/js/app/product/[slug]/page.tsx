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
import { buildMetadata, siteUrl } from "@/lib/seo";
import { fetchStorefrontProduct, fetchStorefrontProducts } from "@/lib/storefront-products";

export const dynamic = "force-dynamic";

type ProductPageProps = {
  params: Promise<{
    slug: string;
  }>;
};

export async function generateMetadata({
  params,
}: ProductPageProps): Promise<Metadata> {
  const { slug } = await params;
  const product = await fetchStorefrontProduct(slug);

  if (!product) {
    return {};
  }

  return {
    ...buildMetadata({
      title: product.name,
      description: product.description || `${product.name} Karacabey Gross Market online sipariş.`,
      path: `/product/${product.slug}`,
      image: product.image,
      keywords: [product.name, product.brand, product.category, "ürün detay", "satın al"],
    }),
  };
}

export default async function ProductPage({ params }: ProductPageProps) {
  const { slug } = await params;
  const product = await fetchStorefrontProduct(slug);

  if (!product) {
    notFound();
  }

  const categorySlug = product.category !== "genel" ? product.category : undefined;
  const { products: relatedCandidates } = await fetchStorefrontProducts({
    category: categorySlug,
    perPage: 8,
  });
  const primaryRelatedProducts = relatedCandidates.filter((item) => item.slug !== product.slug).slice(0, 8);
  const relatedProducts = primaryRelatedProducts.length > 0
    ? primaryRelatedProducts
    : (await fetchStorefrontProducts({ perPage: 8 })).products.filter((item) => item.slug !== product.slug).slice(0, 8);

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "Product",
    name: product.name,
    brand: product.brand,
    sku: product.slug,
    category: product.category,
    url: `${siteUrl}/product/${product.slug}`,
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
          <ProductSlider products={relatedProducts} />
        </section>
      </main>
    </GuestLayout>
  );
}
