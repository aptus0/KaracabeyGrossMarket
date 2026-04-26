import Link from "next/link";
import type { KgmCartItem } from "@/lib/catalog";
import { CheckoutSummary } from "@/app/_components/CheckoutSummary";

type CartDrawerProps = {
  items: KgmCartItem[];
};

export function CartDrawer({ items }: CartDrawerProps) {
  return (
    <aside className="cart-drawer" aria-label="Sepet">
      <CheckoutSummary items={items} />
      <Link className="primary-action" href="/checkout">
        Checkout
      </Link>
    </aside>
  );
}
