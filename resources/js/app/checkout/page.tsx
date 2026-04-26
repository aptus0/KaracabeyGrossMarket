import type { Metadata } from "next";
import { CheckoutSummary } from "@/app/_components/CheckoutSummary";
import { PaymentIframeBox } from "@/app/_components/PaymentIframeBox";
import { AppLayout } from "@/app/_layouts/AppLayout";
import { cartPreview, products } from "@/lib/catalog";

export const metadata: Metadata = {
  title: "Checkout",
  robots: {
    index: false,
    follow: false,
  },
};

export default function CheckoutPage() {
  return (
    <AppLayout>
      <div className="checkout-page">
        <section className="checkout-form">
          <p className="eyebrow">Güvenli ödeme</p>
          <h1>Checkout</h1>
          <form className="form-stack" action={`${process.env.NEXT_PUBLIC_API_URL ?? ""}/api/v1/checkout/paytr`} method="post">
            <label>
              Ad Soyad
              <input name="customer[name]" autoComplete="name" required />
            </label>
            <label>
              E-posta
              <input name="customer[email]" type="email" autoComplete="email" required />
            </label>
            <label>
              Telefon
              <input name="customer[phone]" autoComplete="tel" required />
            </label>
            <label>
              Teslimat Adresi
              <textarea name="shipping[address]" autoComplete="street-address" required />
            </label>
            {cartPreview.map((item, index) => (
              <input
                key={item.slug}
                type="hidden"
                name={`items[${index}][product_id]`}
                value={products.findIndex((product) => product.slug === item.slug) + 1}
              />
            ))}
            {cartPreview.map((item, index) => (
              <input
                key={`${item.slug}-qty`}
                type="hidden"
                name={`items[${index}][quantity]`}
                value={item.quantity}
              />
            ))}
            <button className="primary-action" type="submit">
              PayTR ile Ödemeye Devam Et
            </button>
          </form>
          <PaymentIframeBox />
        </section>

        <CheckoutSummary items={cartPreview} />
      </div>
    </AppLayout>
  );
}
