import type { Metadata } from "next";
import Link from "next/link";
import { AddressCard } from "@/app/_components/AddressCard";
import { CargoTrackingBox } from "@/app/_components/CargoTrackingBox";
import { OrderCard } from "@/app/_components/OrderCard";
import { AppLayout } from "@/app/_layouts/AppLayout";
import { accountAddresses, accountOrders } from "@/lib/catalog";

export const metadata: Metadata = {
  title: "Hesabım",
  robots: {
    index: false,
    follow: false,
  },
};

export default function AccountPage() {
  return (
    <AppLayout sidebar>
      <section className="account-heading">
        <div>
          <p className="eyebrow">Müşteri paneli</p>
          <h1>Hesabım</h1>
        </div>
        <Link className="secondary-action" href="/products">
          Alışverişe Dön
        </Link>
      </section>

      <section className="info-grid" aria-label="Hesap özeti">
        <article className="info-card">
          <strong>Aktif Sipariş</strong>
          <p>KGM260426A1 ödeme bekliyor.</p>
        </article>
        <article className="info-card">
          <strong>Kayıtlı Kart</strong>
          <p>Visa **** 4242, PayTR token ile saklanır.</p>
        </article>
        <article className="info-card">
          <strong>Teslimat</strong>
          <p>Karacabey merkez için hızlı teslimat adresi hazır.</p>
        </article>
      </section>

      <section className="content-band content-band--narrow" id="orders">
        <div className="section-heading">
          <p className="eyebrow">Siparişlerim</p>
          <h2>Son siparişler</h2>
        </div>
        <div className="info-grid">
          {accountOrders.map((order) => (
            <OrderCard key={order.number} order={order} />
          ))}
        </div>
      </section>

      <section className="content-band content-band--narrow" id="addresses">
        <div className="section-heading">
          <p className="eyebrow">Adreslerim</p>
          <h2>Teslimat adresleri</h2>
        </div>
        <div className="info-grid">
          {accountAddresses.map((address) => (
            <AddressCard key={address.title} address={address} />
          ))}
        </div>
      </section>

      <CargoTrackingBox code="KGM-KARGO-260426" status="Hazırlanıyor" />
    </AppLayout>
  );
}
