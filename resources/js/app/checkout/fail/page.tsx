import Link from "next/link";
import { AppLayout } from "@/app/_layouts/AppLayout";

export default function CheckoutFailPage() {
  return (
    <AppLayout>
      <section className="result-panel">
        <p className="eyebrow">Ödeme tamamlanmadı</p>
        <h1>İşlemi tekrar deneyebilirsiniz.</h1>
        <p>Kartınızdan çekim yapılmadıysa checkout ekranına dönüp farklı bir yöntem seçebilirsiniz.</p>
        <div className="split-actions">
          <Link className="primary-action" href="/checkout">
            Checkout
          </Link>
          <Link className="secondary-action" href="/products">
            Ürünler
          </Link>
        </div>
      </section>
    </AppLayout>
  );
}
