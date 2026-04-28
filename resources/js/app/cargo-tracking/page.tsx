import type { Metadata } from "next";
import Link from "next/link";
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
            <p className="eyebrow">Takip</p>
            <h2>Siparişlerinizin durumunu takip edin</h2>
          </div>
        </div>

        <div className="rounded-[24px] border border-[#E4E7EB] bg-white p-8 text-center">
          <p className="text-sm leading-7 text-[#6B7177]">
            Kargo durumunuzu takip etmek için{" "}
            <Link href="/account#orders" className="font-semibold text-[#FF7A00] underline-offset-2 hover:underline">
              hesabınızdaki siparişlerinize
            </Link>{" "}
            göz atabilirsiniz.
          </p>
        </div>
      </section>
    </AppLayout>
  );
}
