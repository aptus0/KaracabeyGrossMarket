import type { Metadata } from "next";
import Link from "next/link";

import { ProductGrid } from "@/app/_components/ProductGrid";
import { AppLayout } from "@/app/_layouts/AppLayout";
import { products } from "@/lib/catalog";
import { buildMetadata } from "@/lib/seo";

export const metadata: Metadata = buildMetadata({
  title: "Favoriler",
  description: "Karacabey Gross Market favori ürünler listeniz.",
  path: "/favorites",
  keywords: ["favoriler", "kayıtlı ürünler", "tekrar sipariş"],
  robots: {
    index: false,
    follow: false,
  },
});

export default function FavoritesPage() {
  return (
    <AppLayout>
      <section className="account-heading">
        <div>
          <p className="eyebrow">Kayıtlı Ürünler</p>
          <h1>Favoriler</h1>
        </div>

        <Link className="secondary-action" href="/products">
          Alışverişe Dön
        </Link>
      </section>

      <section className="content-band content-band--narrow">
        <div className="section-heading">
          <div>
            <p className="eyebrow">Hazır Liste</p>
            <h2>Tekrar almak isteyebileceğin ürünler</h2>
          </div>
        </div>

        <ProductGrid products={products.slice(0, 4)} />
      </section>
    </AppLayout>
  );
}
