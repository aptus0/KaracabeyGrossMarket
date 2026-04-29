"use client";

import { useState } from "react";
import { CreditCard, ShieldCheck, Truck } from "lucide-react";
import { FavoriteButton } from "@/app/_components/FavoriteButton";
import { AddToCartButton } from "@/app/_components/AddToCartButton";

type ProductPurchasePanelProps = {
  productSlug: string;
};

export function ProductPurchasePanel({ productSlug }: ProductPurchasePanelProps) {
  const [quantity, setQuantity] = useState(1);

  return (
    <div className="purchase-box">
      <div className="purchase-box__head">
        <div>
          <strong>Hızlı Sipariş</strong>
          <span>Sepete ekle, teslimat ve ödeme adımlarını tamamla.</span>
        </div>
        <FavoriteButton productSlug={productSlug} />
      </div>

      <div className="purchase-box__controls">
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
        <AddToCartButton productSlug={productSlug} quantity={quantity} className="purchase-box__cart-button" />
      </div>

      <div className="purchase-box__benefits" aria-label="Sipariş avantajları">
        <span><Truck size={15} /> Hızlı teslimat</span>
        <span><ShieldCheck size={15} /> Güvenli alışveriş</span>
        <span><CreditCard size={15} /> Kolay ödeme</span>
      </div>
    </div>
  );
}
