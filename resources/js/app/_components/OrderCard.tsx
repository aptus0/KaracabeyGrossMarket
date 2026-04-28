import Link from "next/link";
import { Package } from "lucide-react";
import { formatCartMoney, orderStatusColor, formatOrderDate } from "@/lib/account";
import type { UserOrder } from "@/lib/account";
import { cn } from "@/lib/utils";

type OrderCardProps = {
  order: UserOrder;
};

export function OrderCard({ order }: OrderCardProps) {
  return (
    <article className="info-card grid gap-3">
      <div className="flex items-start gap-3">
        <div className="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-[#FFF0E0] text-[#FF7A00]">
          <Package size={18} />
        </div>
        <div className="min-w-0 flex-1">
          <p className="font-mono text-xs font-bold tracking-wider text-[#9AA3AF]">
            {order.merchant_oid}
          </p>
          <strong className={cn("block text-sm font-black", orderStatusColor(order.status))}>
            {order.status_label}
          </strong>
          <p className="text-xs text-[#9AA3AF]">{formatOrderDate(order.created_at)}</p>
        </div>
        <strong className="shrink-0 text-sm font-black text-[#2B2F36]">
          {formatCartMoney(order.total_cents)}
        </strong>
      </div>

      {order.items.length > 0 ? (
        <ul className="grid gap-1 border-t border-[#EEF1F4] pt-3">
          {order.items.slice(0, 3).map((item) => (
            <li key={item.id} className="flex items-center justify-between text-xs text-[#6B7177]">
              <span className="min-w-0 truncate">{item.name}</span>
              <span className="ml-2 shrink-0 font-semibold">
                {item.quantity} × {formatCartMoney(item.unit_price_cents)}
              </span>
            </li>
          ))}
          {order.items.length > 3 ? (
            <li className="text-xs text-[#9AA3AF]">
              +{order.items.length - 3} ürün daha
            </li>
          ) : null}
        </ul>
      ) : null}
    </article>
  );
}
