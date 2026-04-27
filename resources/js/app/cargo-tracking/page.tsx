import type { Metadata } from "next";
import Link from "next/link";

import { CargoTrackingBox } from "@/app/_components/CargoTrackingBox";
import { AppLayout } from "@/app/_layouts/AppLayout";
import { buildMetadata } from "@/lib/seo";

export const metadata: Metadata = buildMetadata({
  title: "Kargo Takip",
  description: "Karacabey Gross Market sipariş teslimat ve kargo takip ekranı.",
  path: "/cargo-tracking",
  keywords: ["kargo takip", "teslimat", "sipariş durumu"],
  robots: {
    index: false,
    follow: false,
  },
});

export default function CargoTrackingPage() {
  return (
    <AppLayout>
      <section className="account-heading">
        <div>
          <p className="eyebrow">Teslimat</p>
          <h1>Kargo Takip</h1>
        </div>

        <Link className="secondary-action" href="/account#orders">
          Siparişlerime Git
        </Link>
      </section>

      <section className="content-band content-band--narrow">
        <div className="section-heading">
          <div>
            <p className="eyebrow">Canlı Durum</p>
            <h2>Aktif gönderin hazırlanıyor</h2>
          </div>
        </div>

        <CargoTrackingBox
          code="KGM-KARGO-260426"
          status="Hazırlanıyor"
        />
      </section>
    </AppLayout>
  );
}
