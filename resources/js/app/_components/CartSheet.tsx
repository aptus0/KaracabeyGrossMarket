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
import { useCartStore } from "@/lib/cart-store";

export function CartSheet() {
  const isOpen = useCartStore((state) => state.isSheetOpen);
  const items = useCartStore((state) => state.items);
  const closeSheet = useCartStore((state) => state.closeSheet);
  const count = useCartStore((state) => state.count());

  return (
    <Sheet open={isOpen} onOpenChange={(open) => (open ? useCartStore.getState().openSheet() : closeSheet())}>
      <SheetContent side="right" className="w-full max-w-[440px]">
        <SheetHeader>
          <div className="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#FFF0E0] text-[#FF7A00]">
            <ShoppingBag size={20} />
          </div>
          <SheetTitle>Sepetiniz</SheetTitle>
          <SheetDescription>
            {count > 0
              ? `${count} ürün canlı olarak güncel tutuluyor.`
              : "Sepete ürün eklediğinizde burada anında göreceksiniz."}
          </SheetDescription>
        </SheetHeader>

        <div className="flex-1 overflow-y-auto px-6 py-5">
          <CheckoutSummary
            editable
            title="Mini Cart"
            description="Ürünleri burada hızlıca arttırıp azaltabilir veya checkout’a geçebilirsiniz."
          />
        </div>

        <SheetFooter>
          <Button asChild className="h-12 rounded-xl" disabled={items.length === 0}>
            <Link href="/checkout" onClick={closeSheet}>
              Ödemeye Geç
            </Link>
          </Button>
          <Button asChild variant="secondary" className="h-12 rounded-xl">
            <Link href="/products" onClick={closeSheet}>
              Alışverişe Devam Et
            </Link>
          </Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}
