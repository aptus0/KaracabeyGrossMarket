"use client";

import { useState } from "react";
import { FavoriteButton } from "@/app/_components/FavoriteButton";
import { AddToCartButton } from "@/app/_components/AddToCartButton";

type ProductPurchasePanelProps = {
  productSlug: string;
};

export function ProductPurchasePanel({ productSlug }: ProductPurchasePanelProps) {
  const [quantity, setQuantity] = useState(1);

  return (
    <div className="purchase-box">
      <label className="quantity-selector">
        <span>Adet</span>
        <input
          type="number"
          min={1}
          max={99}
          value={quantity}
          onChange={(event) => {
            const nextQuantity = Number(event.target.value) || 1;
            setQuantity(Math.max(1, Math.min(99, nextQuantity)));
          }}
        />
      </label>
      <AddToCartButton productSlug={productSlug} quantity={quantity} className="min-w-[180px]" />
      <FavoriteButton productSlug={productSlug} />
    </div>
  );
}
