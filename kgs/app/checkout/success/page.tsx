import Link from "next/link";
import { ShellHeader } from "@/app/_components/ShellHeader";

export default function CheckoutSuccessPage() {
  return (
    <>
      <ShellHeader />
      <main className="s50">
        <section className="s51">
          <p className="s7">Ödeme alındı</p>
          <h1>Siparişiniz hazırlanıyor.</h1>
          <p className="s9">
            PayTR doğrulaması tamamlandıktan sonra sipariş durumunuz hesabınıza yansır.
          </p>
          <div className="s53">
            <Link className="s54" href="/account">
              Hesabım
            </Link>
            <Link className="s55" href="/products">
              Alışverişe Devam
            </Link>
          </div>
        </section>
      </main>
    </>
  );
}
