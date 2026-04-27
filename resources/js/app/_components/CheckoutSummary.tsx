"use client";

import Image from "next/image";
import { Minus, Plus, ShoppingBag, Trash2 } from "lucide-react";
import { Button } from "@/app/_components/ui/button";
import { formatCartMoney, type CartLineItem } from "@/lib/cart";
import { useCartStore } from "@/lib/cart-store";
import { cn } from "@/lib/utils";

type CheckoutSummaryProps = {
  items?: CartLineItem[];
  title?: string;
  description?: string;
  editable?: boolean;
  className?: string;
};

export function CheckoutSummary({
  items,
  title = "Sipariş Özeti",
  description = "Sepetinizdeki ürünler canlı olarak güncellenir.",
  editable = false,
  className,
}: CheckoutSummaryProps) {
  const storeItems = useCartStore((state) => state.items);
  const subtotal = useCartStore((state) => state.subtotal_cents);
  const total = useCartStore((state) => state.total_cents);
  const status = useCartStore((state) => state.status);
  const updateItemQuantity = useCartStore((state) => state.updateItemQuantity);
  const removeItem = useCartStore((state) => state.removeItem);

  const resolvedItems = items ?? storeItems;
  const resolvedSubtotal = items
    ? items.reduce((sum, item) => sum + item.line_total_cents, 0)
    : subtotal;
  const resolvedTotal = items
    ? items.reduce((sum, item) => sum + item.line_total_cents, 0)
    : total;

  if (resolvedItems.length === 0) {
    return (
      <section className={cn("checkout-summary rounded-[24px] border border-[#E4E7EB] bg-white p-6", className)}>
        <div className="grid gap-3 text-center">
          <div className="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#FFF1E1] text-[#FF7A00]">
            <ShoppingBag size={22} />
          </div>
          <div className="space-y-1">
            <p className="text-xs font-black uppercase tracking-[0.16em] text-[#FF7A00]">Sepet</p>
            <h2 className="text-xl font-black text-[#2B2F36]">Sepetiniz henüz boş</h2>
          </div>
          <p className="text-sm leading-6 text-[#6B7177]">
            Ürün eklediğiniz anda mini-cart ve checkout özeti burada senkron şekilde görünecek.
          </p>
        </div>
      </section>
    );
  }

  return (
    <section className={cn("checkout-summary rounded-[24px] border border-[#E4E7EB] bg-white p-6", className)}>
      <div className="mb-5 grid gap-1">
        <p className="text-xs font-black uppercase tracking-[0.16em] text-[#FF7A00]">Sepet</p>
        <h2 className="text-2xl font-black text-[#2B2F36]">{title}</h2>
        <p className="text-sm leading-6 text-[#6B7177]">{description}</p>
      </div>

      <ul className="grid gap-4">
        {resolvedItems.map((item) => (
          <li
            key={item.id}
            className="grid gap-3 rounded-2xl border border-[#EEF1F4] bg-[#FCFDFE] p-4"
          >
            <div className="flex items-start gap-3">
              <div className="relative h-16 w-16 shrink-0 overflow-hidden rounded-2xl border border-[#EEF1F4] bg-white">
                {item.product.image_url ? (
                  <Image
                    src={item.product.image_url}
                    alt={item.product.name}
                    fill
                    sizes="64px"
                    className="object-cover"
                  />
                ) : (
                  <div className="flex h-full items-center justify-center text-xs font-black text-[#6B7177]">
                    KGM
                  </div>
                )}
              </div>

              <div className="min-w-0 flex-1">
                <div className="flex items-start justify-between gap-3">
                  <div className="space-y-1">
                    <strong className="line-clamp-2 text-sm font-black text-[#2B2F36]">
                      {item.product.name}
                    </strong>
                    <p className="text-xs font-semibold text-[#6B7177]">
                      {item.product.brand ?? "Karacabey Gross Market"}
                    </p>
                  </div>
                  <strong className="text-sm font-black text-[#2B2F36]">
                    {formatCartMoney(item.line_total_cents)}
                  </strong>
                </div>

                <div className="mt-3 flex flex-wrap items-center justify-between gap-3">
                  <span className="rounded-full bg-[#FFF3E6] px-3 py-1 text-xs font-black uppercase tracking-[0.1em] text-[#FF7A00]">
                    {item.quantity} adet
                  </span>

                  {editable ? (
                    <div className="flex items-center gap-2">
                      <button
                        type="button"
                        className="inline-flex h-9 w-9 items-center justify-center rounded-full border border-[#E4E7EB] bg-white text-[#2B2F36] transition hover:bg-[#FFF8F0]"
                        onClick={() => updateItemQuantity(item.id, Math.max(1, item.quantity - 1))}
                        disabled={status === "updating" || item.quantity <= 1}
                        aria-label={`${item.product.name} miktarını azalt`}
                      >
                        <Minus size={15} />
                      </button>
                      <span className="min-w-8 text-center text-sm font-black text-[#2B2F36]">
                        {item.quantity}
                      </span>
                      <button
                        type="button"
                        className="inline-flex h-9 w-9 items-center justify-center rounded-full border border-[#E4E7EB] bg-white text-[#2B2F36] transition hover:bg-[#FFF8F0]"
                        onClick={() => updateItemQuantity(item.id, Math.min(99, item.quantity + 1))}
                        disabled={status === "updating" || item.quantity >= 99}
                        aria-label={`${item.product.name} miktarını arttır`}
                      >
                        <Plus size={15} />
                      </button>
                      <button
                        type="button"
                        className="inline-flex h-9 w-9 items-center justify-center rounded-full border border-[#F3D4CF] bg-white text-[#A32A18] transition hover:bg-[#FFF3F1]"
                        onClick={() => removeItem(item.id)}
                        disabled={status === "updating"}
                        aria-label={`${item.product.name} ürününü sil`}
                      >
                        <Trash2 size={15} />
                      </button>
                    </div>
                  ) : null}
                </div>
              </div>
            </div>
          </li>
        ))}
      </ul>

      <div className="mt-5 grid gap-3 rounded-2xl border border-[#EEF1F4] bg-[#FAFBFC] p-4">
        <div className="flex items-center justify-between text-sm text-[#6B7177]">
          <span>Ara Toplam</span>
          <strong className="font-black text-[#2B2F36]">{formatCartMoney(resolvedSubtotal)}</strong>
        </div>
        <div className="checkout-summary__total flex items-center justify-between border-t border-[#E4E7EB] pt-3">
          <span className="text-sm font-bold text-[#6B7177]">Toplam</span>
          <strong className="text-xl font-black text-[#2B2F36]">{formatCartMoney(resolvedTotal)}</strong>
        </div>
      </div>

      {editable ? (
        <div className="mt-4">
          <Button
            type="button"
            variant="secondary"
            className="h-11 w-full rounded-xl"
            onClick={() => useCartStore.getState().clearCart()}
            disabled={status === "updating"}
          >
            Sepeti Temizle
          </Button>
        </div>
      ) : null}
    </section>
  );
}
