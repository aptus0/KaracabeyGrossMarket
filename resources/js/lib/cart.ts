export type CartProduct = {
  id: number;
  name: string;
  slug: string;
  brand?: string | null;
  price_cents: number;
  price: string;
  stock_quantity: number;
  image_url?: string | null;
};

export type CartLineItem = {
  id: number;
  quantity: number;
  line_total_cents: number;
  product: CartProduct;
};

export type CartData = {
  cart_token: string | null;
  items: CartLineItem[];
  subtotal_cents: number;
  total_cents: number;
};

export const emptyCart: CartData = {
  cart_token: null,
  items: [],
  subtotal_cents: 0,
  total_cents: 0,
};

export function formatCartMoney(valueInCents: number) {
  return new Intl.NumberFormat("tr-TR", {
    style: "currency",
    currency: "TRY",
  }).format(valueInCents / 100);
}

export function cartItemCount(items: CartLineItem[]) {
  return items.reduce((total, item) => total + item.quantity, 0);
}

export function normalizeCart(cart?: Partial<CartData> | null): CartData {
  return {
    cart_token: cart?.cart_token ?? null,
    items: cart?.items ?? [],
    subtotal_cents: cart?.subtotal_cents ?? 0,
    total_cents: cart?.total_cents ?? 0,
  };
}
