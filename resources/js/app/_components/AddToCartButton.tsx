"use client";

import { ShoppingCart } from "lucide-react";
import { useState } from "react";
import { Button } from "@/app/_components/ui/button";
import { cn } from "@/lib/utils";
import { extractErrorMessage } from "@/lib/api";
import { useCartStore } from "@/lib/cart-store";

type AddToCartButtonProps = {
  productSlug: string;
  quantity?: number;
  className?: string;
  label?: string;
};

export function AddToCartButton({
  productSlug,
  quantity = 1,
  className,
  label = "Sepete Ekle",
}: AddToCartButtonProps) {
  const addItemBySlug = useCartStore((state) => state.addItemBySlug);
  const [isPending, setIsPending] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleAddToCart() {
    setError(null);
    setIsPending(true);

    try {
      await addItemBySlug(productSlug, quantity, { openSheet: true });
    } catch (caughtError) {
      setError(extractErrorMessage(caughtError, "Ürün sepete eklenemedi."));
    } finally {
      setIsPending(false);
    }
  }

  return (
    <div className="grid gap-2">
      <Button
        type="button"
        className={cn("h-11 rounded-xl", className)}
        onClick={handleAddToCart}
        disabled={isPending}
      >
        <ShoppingCart size={17} />
        {isPending ? "Ekleniyor" : label}
      </Button>
      {error ? <p className="text-xs font-semibold text-[#A32A18]">{error}</p> : null}
    </div>
  );
}
