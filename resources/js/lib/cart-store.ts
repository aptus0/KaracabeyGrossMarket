"use client";

import { create } from "zustand";
import { createJSONStorage, persist } from "zustand/middleware";
import { apiRequest, extractErrorMessage } from "@/lib/api";
import { useAuthStore } from "@/lib/auth-store";
import {
  cartItemCount,
  emptyCart,
  normalizeCart,
  type CartData,
} from "@/lib/cart";

type CartStatus = "idle" | "loading" | "updating" | "error";

type CartStore = CartData & {
  status: CartStatus;
  error: string | null;
  isSheetOpen: boolean;
  isHydrated: boolean;
  lastAddedItem: { product: any; quantity: number } | null;
  markHydrated: () => void;
  clearLastAddedItem: () => void;
  initialize: (options?: { silent?: boolean }) => Promise<CartData>;
  addItemBySlug: (slug: string, quantity?: number, options?: { openSheet?: boolean }) => Promise<CartData>;
  updateItemQuantity: (itemId: number, quantity: number) => Promise<CartData>;
  removeItem: (itemId: number) => Promise<CartData>;
  clearCart: () => Promise<CartData>;
  openSheet: () => void;
  closeSheet: () => void;
  count: () => number;
};

const productIdCache = new Map<string, number>();

export const useCartStore = create<CartStore>()(
  persist(
    (set, get) => ({
      ...emptyCart,
      status: "idle",
      error: null,
      isSheetOpen: false,
      isHydrated: false,
      lastAddedItem: null,
      markHydrated: () => set({ isHydrated: true }),
      clearLastAddedItem: () => set({ lastAddedItem: null }),
      initialize: async (options) => {
        const status = options?.silent ? "idle" : "loading";
        set({ status, error: null });

        try {
          const cart = await requestCart("/api/v1/cart", {
            method: "GET",
          }, get().cart_token);

          const nextCart = normalizeCart(cart);

          set({
            ...nextCart,
            status: "idle",
            error: null,
            isHydrated: true,
          });

          return nextCart;
        } catch (error) {
          set({
            status: "error",
            error: extractErrorMessage(error, "Sepet yüklenemedi."),
            isHydrated: true,
          });

          throw error;
        }
      },
      addItemBySlug: async (slug, quantity = 1, options = { openSheet: true }) => {
        set({ status: "updating", error: null });

        try {
          const productId = await resolveProductId(slug);
          const cart = await requestCart(
            "/api/v1/cart/items",
            {
              method: "POST",
              body: JSON.stringify({
                product_id: productId,
                quantity,
              }),
            },
            get().cart_token,
          );

          const nextCart = normalizeCart(cart);
          const addedProduct = nextCart.items.find((item) => item.product?.slug === slug)?.product;

          set({
            ...nextCart,
            status: "idle",
            error: null,
            isHydrated: true,
            isSheetOpen: options.openSheet ?? true,
            lastAddedItem: addedProduct ? { product: addedProduct, quantity } : null,
          });

          return nextCart;
        } catch (error) {
          set({
            status: "error",
            error: extractErrorMessage(error, "Ürün sepete eklenemedi."),
          });

          throw error;
        }
      },
      updateItemQuantity: async (itemId, quantity) => {
        set({ status: "updating", error: null });

        try {
          const cart = await requestCart(
            `/api/v1/cart/items/${itemId}`,
            {
              method: "PATCH",
              body: JSON.stringify({ quantity }),
            },
            get().cart_token,
          );

          const nextCart = normalizeCart(cart);

          set({
            ...nextCart,
            status: "idle",
            error: null,
            isHydrated: true,
          });

          return nextCart;
        } catch (error) {
          set({
            status: "error",
            error: extractErrorMessage(error, "Sepet güncellenemedi."),
          });

          throw error;
        }
      },
      removeItem: async (itemId) => {
        set({ status: "updating", error: null });

        try {
          const cart = await requestCart(
            `/api/v1/cart/items/${itemId}`,
            {
              method: "DELETE",
            },
            get().cart_token,
          );

          const nextCart = normalizeCart(cart);

          set({
            ...nextCart,
            status: "idle",
            error: null,
            isHydrated: true,
          });

          return nextCart;
        } catch (error) {
          set({
            status: "error",
            error: extractErrorMessage(error, "Ürün sepetten silinemedi."),
          });

          throw error;
        }
      },
      clearCart: async () => {
        set({ status: "updating", error: null });

        try {
          const cart = await requestCart(
            "/api/v1/cart",
            {
              method: "DELETE",
            },
            get().cart_token,
          );

          const nextCart = normalizeCart(cart);

          set({
            ...nextCart,
            status: "idle",
            error: null,
            isHydrated: true,
          });

          return nextCart;
        } catch (error) {
          set({
            status: "error",
            error: extractErrorMessage(error, "Sepet temizlenemedi."),
          });

          throw error;
        }
      },
      openSheet: () => set({ isSheetOpen: true }),
      closeSheet: () => set({ isSheetOpen: false }),
      count: () => cartItemCount(get().items),
    }),
    {
      name: "kgm-cart-store",
      storage: createJSONStorage(() => localStorage),
      partialize: (state) => ({
        cart_token: state.cart_token,
        items: state.items,
        subtotal_cents: state.subtotal_cents,
        total_cents: state.total_cents,
      }),
      onRehydrateStorage: () => (state) => {
        state?.markHydrated();
      },
    },
  ),
);

async function requestCart(path: string, init: RequestInit, cartToken: string | null) {
  const authToken = useAuthStore.getState().token;

  return apiRequest<CartData>(path, {
    ...init,
    headers: {
      ...(authToken ? { Authorization: `Bearer ${authToken}` } : {}),
      ...(!authToken && cartToken ? { "X-Cart-Token": cartToken } : {}),
      ...(init.headers ?? {}),
    },
  });
}

async function resolveProductId(slug: string) {
  const cachedProductId = productIdCache.get(slug);

  if (cachedProductId) {
    return cachedProductId;
  }

  const product = await apiRequest<{ id: number }>(`/api/v1/products/${slug}`);
  productIdCache.set(slug, product.id);

  return product.id;
}
