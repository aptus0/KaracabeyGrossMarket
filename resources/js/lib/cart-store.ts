import { create } from "zustand";
import { cartPreview, type KgmCartItem } from "@/lib/catalog";

type CartState = {
  items: KgmCartItem[];
  setItems: (items: KgmCartItem[]) => void;
  clear: () => void;
  count: () => number;
  total: () => number;
};

export const useCartStore = create<CartState>((set, get) => ({
  items: cartPreview,
  setItems: (items) => set({ items }),
  clear: () => set({ items: [] }),
  count: () => get().items.reduce((total, item) => total + item.quantity, 0),
  total: () => get().items.reduce((total, item) => total + item.price * item.quantity, 0),
}));
