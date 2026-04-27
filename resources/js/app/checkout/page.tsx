import type { Metadata } from "next";
import { CheckoutForm } from "@/app/_components/CheckoutForm";
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
  const checkoutItems = cartPreview.map((item) => ({
    productId: products.findIndex((product) => product.slug === item.slug) + 1,
    quantity: item.quantity,
  }));

  return (
    <AppLayout>
      <div className="checkout-page">
        <section className="checkout-form">
          <p className="eyebrow">Güvenli ödeme</p>
          <h1>Checkout</h1>
          <CheckoutForm items={checkoutItems} />
          <PaymentIframeBox />
        </section>

        <CheckoutSummary items={cartPreview} />
      </div>
    </AppLayout>
  );
}
