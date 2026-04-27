import Link from "next/link";
import { AppLayout } from "@/app/_layouts/AppLayout";

export default function CheckoutSuccessPage() {
  return (
    <AppLayout>
      <section className="result-panel">
        <p className="eyebrow">Ödeme alındı</p>
        <h1>Siparişiniz hazırlanıyor.</h1>
        <p>Ödeme doğrulaması tamamlandıktan sonra sipariş durumunuz hesabınıza yansır.</p>
        <div className="split-actions">
          <Link className="primary-action" href="/account">
            Hesabım
          </Link>
          <Link className="secondary-action" href="/products">
            Alışverişe Devam
          </Link>
        </div>
      </section>
    </AppLayout>
  );
}
