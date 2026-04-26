import type { KgmOrder } from "@/lib/catalog";
import { formatPrice } from "@/lib/catalog";

type OrderCardProps = {
  order: KgmOrder;
};

export function OrderCard({ order }: OrderCardProps) {
  return (
    <article className="info-card">
      <strong>{order.number}</strong>
      <p>{order.status}</p>
      <p>{formatPrice(order.total)}</p>
    </article>
  );
}
