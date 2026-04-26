import Link from "next/link";
import { ShellHeader } from "@/app/_components/ShellHeader";

export default function CheckoutFailPage() {
  return (
    <>
      <ShellHeader />
      <main className="s50">
        <section className="s51">
          <p className="s7">Ödeme tamamlanmadı</p>
          <h1>İşlemi tekrar deneyebilirsiniz.</h1>
          <p className="s9">
            Kartınızdan çekim yapılmadıysa checkout ekranına dönüp farklı bir yöntem seçebilirsiniz.
          </p>
          <div className="s53">
            <Link className="s54" href="/checkout">
              Checkout
            </Link>
            <Link className="s55" href="/products">
              Ürünler
            </Link>
          </div>
        </section>
      </main>
    </>
  );
}
