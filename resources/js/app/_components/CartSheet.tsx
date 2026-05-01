"use client";

import Link from "next/link";
import { ShoppingBag } from "lucide-react";
import { CheckoutSummary } from "@/app/_components/CheckoutSummary";
import { Button } from "@/app/_components/ui/button";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/app/_components/ui/sheet";
import { cartItemCount } from "@/lib/cart";
import { useCartStore } from "@/lib/cart-store";

export function CartSheet() {
  const isOpen = useCartStore((state) => state.isSheetOpen);
  const items = useCartStore((state) => state.items);
  const openSheet = useCartStore((state) => state.openSheet);
  const closeSheet = useCartStore((state) => state.closeSheet);
  const count = useCartStore((state) => cartItemCount(state.items));

  return (
    <Sheet open={isOpen} onOpenChange={(open) => (open ? openSheet() : closeSheet())}>
      <SheetContent side="right" className="flex flex-col border-none bg-[#F8FAFC] p-0 sm:max-w-[420px]">
        <SheetHeader className="bg-white px-6 pb-4 pt-6 shadow-sm">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#FFF8F0] text-[#FF7A00]">
                <ShoppingBag size={18} />
              </div>
              <div>
                <SheetTitle className="text-base font-black text-[#2B2F36]">Sepetiniz</SheetTitle>
                <SheetDescription className="text-[11px] font-bold text-[#64748B]">
                  {count > 0 ? `${count} Ürün Güncel` : "Sepetiniz Boş"}
                </SheetDescription>
              </div>
            </div>
          </div>
        </SheetHeader>

        <div className="flex-1 overflow-y-auto p-4 sm:p-6">
          <CheckoutSummary
            editable
            title="Sipariş Özeti"
            description="Ürünlerinizi buradan yönetebilirsiniz."
          />
        </div>

        <SheetFooter className="mt-auto grid gap-2 bg-white px-6 py-5 shadow-[0_-4px_20px_rgba(0,0,0,0.03)] sm:flex-col">
          <Button asChild className="h-12 w-full rounded-xl bg-[#FF7A00] text-sm font-black hover:bg-[#E66E00]" disabled={items.length === 0}>
            <Link href="/checkout" onClick={closeSheet}>
              Ödemeye Geç
            </Link>
          </Button>
          <Button asChild variant="ghost" className="h-11 w-full rounded-xl text-xs font-bold text-[#64748B]" onClick={closeSheet}>
            <span>Alışverişe Devam Et</span>
          </Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}
