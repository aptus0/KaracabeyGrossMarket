"use client";

import Link from "next/link";
import { CreditCard, ShieldCheck } from "lucide-react";
import { useEffect } from "react";
import { CheckoutForm } from "@/app/_components/CheckoutForm";
import { CheckoutSummary } from "@/app/_components/CheckoutSummary";
import { useCartStore } from "@/lib/cart-store";

export function CheckoutExperience() {
  const items = useCartStore((state) => state.items);
  const cartToken = useCartStore((state) => state.cart_token);
  const isHydrated = useCartStore((state) => state.isHydrated);
  const status = useCartStore((state) => state.status);
  const appliedCoupon = useCartStore((state) => state.applied_coupon);
  const initializeCart = useCartStore((state) => state.initialize);

  useEffect(() => {
    if (!isHydrated) {
      initializeCart().catch(() => undefined);
    }
  }, [initializeCart, isHydrated]);

  if (isHydrated && items.length === 0) {
    return (
      <section className="rounded-[28px] border border-[#E4E7EB] bg-white p-8 text-center shadow-[0_18px_48px_rgba(43,47,54,0.08)]">
        <p className="eyebrow">Checkout</p>
        <h1 className="mb-3 text-3xl font-black text-[#2B2F36]">Sepetiniz şu an boş görünüyor.</h1>
        <p className="mx-auto max-w-2xl text-sm leading-7 text-[#6B7177]">
          Ürün eklediğiniz anda bu alan canlı sepet verisiyle dolacak ve ödeme akışı hazır hale gelecek.
        </p>
        <div className="mt-6 flex flex-wrap justify-center gap-3">
          <Link className="primary-action" href="/products">
            Ürünlere Git
          </Link>
          <Link className="secondary-action" href="/auth/login">
            Hesabıma Geç
          </Link>
        </div>
      </section>
    );
  }

  return (
    <div className="checkout-page">
      <section className="checkout-form rounded-[28px] border border-[#E4E7EB] bg-white p-6 shadow-[0_18px_48px_rgba(43,47,54,0.08)] sm:p-8">
        <div className="mb-6 grid gap-3">
          <div className="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#FFF0E0] text-[#FF7A00]">
            <CreditCard size={20} />
          </div>
          <p className="eyebrow">Güvenli ödeme</p>
          <h1>Checkout</h1>
          <p className="text-sm leading-7 text-[#6B7177]">
            Sipariş bilgilerinizi tamamlayın, PayTR oturumu açıldığında güvenli ödeme sayfasına yönlendirelim.
          </p>
        </div>

        <div className="mb-6 flex flex-wrap gap-3 rounded-2xl border border-[#EEF1F4] bg-[#FAFBFC] p-4 text-sm text-[#5F6670]">
          <span className="inline-flex items-center gap-2 font-semibold">
            <ShieldCheck size={16} className="text-[#FF7A00]" />
            3D Secure destekli ödeme
          </span>
          <span className="inline-flex items-center gap-2 font-semibold">
            <ShieldCheck size={16} className="text-[#FF7A00]" />
            SSL ile şifrelenmiş checkout
          </span>
        </div>

        <CheckoutForm
          items={items.map((item) => ({
            productId: item.product.id,
            quantity: item.quantity,
          }))}
          cartToken={cartToken}
          couponCode={appliedCoupon?.code ?? null}
          disabled={status === "loading"}
        />
      </section>

      <CheckoutSummary
        editable
        title="Sipariş Özeti"
        description="Header badge, mini-cart ve bu sayfa aynı canlı sepet verisini kullanır."
      />
    </div>
  );
}
