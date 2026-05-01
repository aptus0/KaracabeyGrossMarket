"use client";

import Image from "next/image";
import { Minus, Plus, ShoppingBag, Trash2 } from "lucide-react";
import { CouponInput } from "@/app/_components/CouponInput";
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
  const appliedCoupon = useCartStore((state) => state.applied_coupon);
  const subtotal = useCartStore((state) => state.subtotal_cents);
  const total = useCartStore((state) => state.total_cents);
  const status = useCartStore((state) => state.status);
  const error = useCartStore((state) => state.error);
  const updateItemQuantity = useCartStore((state) => state.updateItemQuantity);
  const removeItem = useCartStore((state) => state.removeItem);
  const applyCoupon = useCartStore((state) => state.applyCoupon);
  const clearCoupon = useCartStore((state) => state.removeCoupon);

  const resolvedItems = items ?? storeItems;
  const resolvedCoupon = items ? null : appliedCoupon;
  const resolvedSubtotal = items
    ? items.reduce((sum, item) => sum + item.line_total_cents, 0)
    : subtotal;
  const discountCents = resolvedCoupon?.discount_cents ?? 0;
  const resolvedTotal = items ? resolvedSubtotal : total;

  async function handleQuantityChange(itemId: number, quantity: number) {
    await updateItemQuantity(itemId, quantity).catch(() => undefined);
  }

  async function handleItemRemoval(itemId: number) {
    await removeItem(itemId).catch(() => undefined);
  }

  if (resolvedItems.length === 0) {
    return (
      <section className={cn("rounded-2xl border border-[#F1F5F9] bg-white p-6", className)}>
        <div className="grid gap-3 text-center">
          <div className="mx-auto inline-flex h-12 w-12 items-center justify-center rounded-xl bg-[#FFF8F0] text-[#FF7A00]">
            <ShoppingBag size={20} />
          </div>
          <div className="space-y-1">
            <h2 className="text-lg font-black text-[#2B2F36]">Sepetiniz Boş</h2>
            <p className="text-sm text-[#64748B]">Henüz ürün eklememişsiniz.</p>
          </div>
        </div>
      </section>
    );
  }

  return (
    <section className={cn("grid gap-4", className)}>
      {/* ── HEADER ── */}
      <div className="px-1">
        <h2 className="text-lg font-black text-[#2B2F36]">{title}</h2>
        <p className="text-xs font-semibold text-[#64748B]">{description}</p>
      </div>

      {/* ── ITEM LIST ── */}
      <ul className="grid gap-2">
        {resolvedItems.map((item) => (
          <li
            key={item.id}
            className="group relative flex items-center gap-3 rounded-xl border border-[#F1F5F9] bg-white p-2.5 transition hover:border-[#FF7A00]/20 hover:bg-[#FFF8F0]/30"
          >
            {/* Image Container */}
            <div className="relative h-14 w-14 shrink-0 overflow-hidden rounded-lg border border-[#F1F5F9] bg-white">
              {item.product.image_url ? (
                <Image
                  src={item.product.image_url}
                  alt={item.product.name}
                  fill
                  sizes="56px"
                  className="object-contain p-1 transition duration-300 group-hover:scale-110"
                />
              ) : (
                <div className="flex h-full items-center justify-center bg-[#F8FAFC] text-[10px] font-black text-[#94A3B8]">
                  KGM
                </div>
              )}
            </div>

            {/* Content */}
            <div className="min-w-0 flex-1 space-y-1.5">
              <div className="flex items-start justify-between gap-2">
                <div className="grid gap-0.5">
                  <h3 className="line-clamp-1 text-xs font-black text-[#2B2F36]">
                    {item.product.name}
                  </h3>
                  <p className="text-[10px] font-bold text-[#64748B]">
                    {item.product.brand ?? "Karacabey Gross"}
                  </p>
                </div>
                <span className="text-xs font-black text-[#2B2F36]">
                  {formatCartMoney(item.line_total_cents)}
                </span>
              </div>

              {/* Controls */}
              <div className="flex items-center justify-between gap-2">
                <div className="flex items-center gap-1">
                  <span className="text-[10px] font-black uppercase tracking-wider text-[#FF7A00]">
                    {item.quantity} Adet
                  </span>
                </div>

                {editable && (
                  <div className="flex items-center gap-1 rounded-lg border border-[#F1F5F9] bg-white p-0.5">
                    <button
                      type="button"
                      className="inline-flex h-6 w-6 items-center justify-center rounded-md text-[#2B2F36] transition hover:bg-[#F1F5F9] disabled:opacity-30"
                      onClick={() => void handleQuantityChange(item.id, item.quantity - 1)}
                      disabled={status === "updating" || item.quantity <= 1}
                    >
                      <Minus size={12} strokeWidth={3} />
                    </button>
                    <span className="min-w-6 text-center text-[11px] font-black text-[#2B2F36]">
                      {item.quantity}
                    </span>
                    <button
                      type="button"
                      className="inline-flex h-6 w-6 items-center justify-center rounded-md text-[#2B2F36] transition hover:bg-[#F1F5F9] disabled:opacity-30"
                      onClick={() => void handleQuantityChange(item.id, item.quantity + 1)}
                      disabled={status === "updating"}
                    >
                      <Plus size={12} strokeWidth={3} />
                    </button>
                    <div className="mx-1 h-3 w-[1px] bg-[#F1F5F9]" />
                    <button
                      type="button"
                      className="inline-flex h-6 w-6 items-center justify-center rounded-md text-[#EF4444] transition hover:bg-[#FEF2F2]"
                      onClick={() => void handleItemRemoval(item.id)}
                      disabled={status === "updating"}
                    >
                      <Trash2 size={12} strokeWidth={2.5} />
                    </button>
                  </div>
                )}
              </div>
            </div>
          </li>
        ))}
      </ul>

      {/* ── COUPON & TOTALS ── */}
      <div className="grid gap-3 rounded-2xl border border-[#F1F5F9] bg-[#F8FAFC]/50 p-4">
        {editable && !items && (
          <CouponInput
            appliedCoupon={resolvedCoupon}
            onApply={applyCoupon}
            onRemove={clearCoupon}
            disabled={status === "updating"}
          />
        )}

        <div className="space-y-2">
          <div className="flex items-center justify-between text-xs font-semibold text-[#64748B]">
            <span>Ara Toplam</span>
            <span className="font-black text-[#2B2F36]">{formatCartMoney(resolvedSubtotal)}</span>
          </div>
          {discountCents > 0 && (
            <div className="flex items-center justify-between text-xs font-semibold text-[#16A34A]">
              <span>İndirim</span>
              <span className="font-black">-{formatCartMoney(discountCents)}</span>
            </div>
          )}
          <div className="flex items-center justify-between border-t border-[#F1F5F9] pt-2">
            <span className="text-sm font-black text-[#2B2F36]">Toplam</span>
            <span className="text-lg font-black text-[#FF7A00]">{formatCartMoney(resolvedTotal)}</span>
          </div>
        </div>
      </div>

      {editable && (
        <Button
          type="button"
          variant="ghost"
          className="h-10 w-full rounded-xl text-xs font-bold text-[#64748B] hover:bg-[#FEF2F2] hover:text-[#EF4444]"
          onClick={() => useCartStore.getState().clearCart()}
          disabled={status === "updating"}
        >
          Sepeti Temizle
        </Button>
      )}

      {error && (
        <p className="rounded-xl bg-[#FEF2F2] px-4 py-3 text-[11px] font-bold text-[#EF4444]">
          {error}
        </p>
      )}
    </section>
  );
}
