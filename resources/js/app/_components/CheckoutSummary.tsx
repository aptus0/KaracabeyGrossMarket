import type { KgmCartItem } from "@/lib/catalog";
import { formatPrice } from "@/lib/catalog";
import { CartItem } from "@/app/_components/CartItem";

type CheckoutSummaryProps = {
  items: KgmCartItem[];
};

export function CheckoutSummary({ items }: CheckoutSummaryProps) {
  const subtotal = items.reduce((sum, item) => sum + item.price * item.quantity, 0);

  return (
    <section className="checkout-summary" aria-label="Sipariş özeti">
      <p className="eyebrow">Sepet</p>
      <ul>
        {items.map((item) => (
          <CartItem key={item.slug} item={item} />
        ))}
      </ul>
      <div className="checkout-summary__total">
        <span>Toplam</span>
        <strong>{formatPrice(subtotal)}</strong>
      </div>
    </section>
  );
}
