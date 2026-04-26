import type { Metadata } from "next";
import Link from "next/link";
import { ShellHeader } from "@/app/_components/ShellHeader";

export const metadata: Metadata = {
  title: "Hesabım",
  robots: {
    index: false,
    follow: false,
  },
};

export default function AccountPage() {
  return (
    <>
      <ShellHeader />
      <main className="s57">
        <section className="s58">
          <div>
            <p className="s7">Müşteri paneli</p>
            <h1>Hesabım</h1>
          </div>
          <Link className="s12" href="/products">
            Alışverişe Dön
          </Link>
        </section>

        <section className="s59" aria-label="Hesap özeti">
          <article className="s60">
            <strong>Aktif Sipariş</strong>
            <p>KGM260426A1 ödeme bekliyor.</p>
          </article>
          <article className="s60">
            <strong>Kayıtlı Kart</strong>
            <p>Visa **** 4242, PayTR token ile saklanır.</p>
          </article>
          <article className="s60">
            <strong>Teslimat</strong>
            <p>Karacabey merkez için hızlı teslimat adresi hazır.</p>
          </article>
        </section>
      </main>
    </>
  );
}
