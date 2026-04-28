"use client";

import { create } from "zustand";
import { createJSONStorage, persist } from "zustand/middleware";
import { ApiRequestError, apiRequest, extractErrorMessage } from "@/lib/api";
import { useAuthStore } from "@/lib/auth-store";
import {
  cartItemCount,
  emptyCart,
  normalizeCart,
  type AppliedCoupon,
  type CartData,
  type CartProduct,
} from "@/lib/cart";

type CartStatus = "idle" | "loading" | "updating" | "error";

type CartStore = CartData & {
  status: CartStatus;
  error: string | null;
  isSheetOpen: boolean;
  isHydrated: boolean;
  lastAddedItem: { product: CartProduct; quantity: number } | null;
  markHydrated: () => void;
  clearLastAddedItem: () => void;
  initialize: (options?: { silent?: boolean }) => Promise<CartData>;
  addItemBySlug: (slug: string, quantity?: number, options?: { openSheet?: boolean }) => Promise<CartData>;
  updateItemQuantity: (itemId: number, quantity: number) => Promise<CartData>;
  removeItem: (itemId: number) => Promise<CartData>;
  clearCart: () => Promise<CartData>;
  applyCoupon: (code: string) => Promise<CartData>;
  removeCoupon: () => Promise<CartData>;
  openSheet: () => void;
  closeSheet: () => void;
  count: () => number;
};

const productIdCache = new Map<string, number>();
let cartTaskQueue: Promise<unknown> = Promise.resolve();

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
        return queueCartTask(async () => {
          try {
            return await syncCartState(get, set, {
              silent: options?.silent,
            });
          } catch (error) {
            set({
              status: "error",
              error: extractErrorMessage(error, "Sepet yüklenemedi."),
              isHydrated: true,
            });

            throw error;
          }
        });
      },
      addItemBySlug: async (slug, quantity = 1, options = { openSheet: true }) => {
        return queueCartTask(async () => {
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
              ensureGuestCartToken(get, set),
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
        });
      },
      updateItemQuantity: async (itemId, quantity) => {
        return queueCartTask(async () => {
          set({ status: "updating", error: null });

          try {
            const cart = await requestCart(
              `/api/v1/cart/items/${itemId}`,
              {
                method: "PATCH",
                body: JSON.stringify({ quantity }),
              },
              ensureGuestCartToken(get, set),
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
            if (shouldRecoverCart(error)) {
              const nextCart = await syncCartState(get, set, {
                silent: true,
              });

              set({
                ...nextCart,
                status: "idle",
                error: "Sepetiniz yenilendi. Lütfen tekrar deneyin.",
                isHydrated: true,
              });

              return nextCart;
            }

            set({
              ...get(),
              status: "error",
              error: extractErrorMessage(error, "Sepet güncellenemedi."),
            });

            return normalizeCart(get());
          }
        });
      },
      removeItem: async (itemId) => {
        return queueCartTask(async () => {
          set({ status: "updating", error: null });

          try {
            const cart = await requestCart(
              `/api/v1/cart/items/${itemId}`,
              {
                method: "DELETE",
              },
              ensureGuestCartToken(get, set),
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
            if (shouldRecoverCart(error)) {
              const nextCart = await syncCartState(get, set, {
                silent: true,
              });

              set({
                ...nextCart,
                status: "idle",
                error: "Sepetiniz yenilendi. Lütfen tekrar deneyin.",
                isHydrated: true,
              });

              return nextCart;
            }

            set({
              ...get(),
              status: "error",
              error: extractErrorMessage(error, "Ürün sepetten silinemedi."),
            });

            return normalizeCart(get());
          }
        });
      },
      clearCart: async () => {
        return queueCartTask(async () => {
          set({ status: "updating", error: null });

          try {
            const cart = await requestCart(
              "/api/v1/cart",
              {
                method: "DELETE",
              },
              ensureGuestCartToken(get, set),
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
        });
      },
      applyCoupon: async (code) => {
        return queueCartTask(async () => {
          set({ status: "updating", error: null });

          try {
            const appliedCoupon = await requestCartCoupon(code, ensureGuestCartToken(get, set));
            const nextCart = normalizeCart({
              ...get(),
              applied_coupon: appliedCoupon,
              total_cents: appliedCoupon.total_cents,
            });

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
              error: extractErrorMessage(error, "Kupon uygulanamadı."),
            });

            throw error;
          }
        });
      },
      removeCoupon: async () => {
        return queueCartTask(async () => {
          set({ status: "updating", error: null });

          try {
            await requestCouponRemoval(ensureGuestCartToken(get, set));
            const nextCart = normalizeCart({
              ...get(),
              applied_coupon: null,
              total_cents: get().subtotal_cents,
            });

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
              error: extractErrorMessage(error, "Kupon kaldırılamadı."),
            });

            throw error;
          }
        });
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
        applied_coupon: state.applied_coupon,
        subtotal_cents: state.subtotal_cents,
        total_cents: state.total_cents,
      }),
      onRehydrateStorage: () => (state) => {
        state?.markHydrated();
      },
    },
  ),
);

function queueCartTask<T>(task: () => Promise<T>) {
  const nextTask = cartTaskQueue
    .catch(() => undefined)
    .then(task);

  cartTaskQueue = nextTask.catch(() => undefined);

  return nextTask;
}

async function syncCartState(
  get: () => CartStore,
  set: (partial: Partial<CartStore>) => void,
  options?: {
    silent?: boolean;
  },
) {
  const status = options?.silent ? "idle" : "loading";
  set({ status, error: null });

  const cart = await requestCart(
    "/api/v1/cart",
    {
      method: "GET",
    },
    ensureGuestCartToken(get, set),
  );

  const nextCart = normalizeCart(cart);

  set({
    ...nextCart,
    status: "idle",
    error: null,
    isHydrated: true,
  });

  return nextCart;
}

function shouldRecoverCart(error: unknown) {
  return error instanceof ApiRequestError && (error.status === 403 || error.status === 404);
}

function ensureGuestCartToken(
  get: () => CartStore,
  set: (partial: Partial<CartStore>) => void,
) {
  if (useAuthStore.getState().token) {
    return null;
  }

  const existingCartToken = get().cart_token;

  if (existingCartToken) {
    return existingCartToken;
  }

  const nextCartToken = createGuestCartToken();

  set({
    cart_token: nextCartToken,
    isHydrated: true,
  });

  return nextCartToken;
}

function createGuestCartToken() {
  if (typeof crypto !== "undefined" && typeof crypto.randomUUID === "function") {
    return crypto.randomUUID();
  }

  return `guest-${Date.now()}-${Math.random().toString(36).slice(2, 10)}`;
}

async function requestCart(path: string, init: RequestInit, cartToken: string | null) {
  const authToken = useAuthStore.getState().token;

  return apiRequest<CartData>(path, {
    ...init,
    cache: "no-store",
    headers: {
      ...(authToken ? { Authorization: `Bearer ${authToken}` } : {}),
      ...(!authToken && cartToken ? { "X-Cart-Token": cartToken } : {}),
      ...(init.headers ?? {}),
    },
  });
}

async function requestCartCoupon(code: string, cartToken: string | null) {
  const authToken = useAuthStore.getState().token;

  return apiRequest<AppliedCoupon>("/api/v1/cart/coupon", {
    method: "POST",
    cache: "no-store",
    body: JSON.stringify({ code }),
    headers: {
      ...(authToken ? { Authorization: `Bearer ${authToken}` } : {}),
      ...(!authToken && cartToken ? { "X-Cart-Token": cartToken } : {}),
    },
  });
}

async function requestCouponRemoval(cartToken: string | null) {
  const authToken = useAuthStore.getState().token;

  return apiRequest<{ removed: boolean }>("/api/v1/cart/coupon", {
    method: "DELETE",
    cache: "no-store",
    headers: {
      ...(authToken ? { Authorization: `Bearer ${authToken}` } : {}),
      ...(!authToken && cartToken ? { "X-Cart-Token": cartToken } : {}),
    },
  });
}

async function resolveProductId(slug: string) {
  const cachedProductId = productIdCache.get(slug);

  if (cachedProductId) {
    return cachedProductId;
  }

  const product = await apiRequest<{ id: number }>(`/api/v1/products/${encodeURIComponent(slug)}`);
  productIdCache.set(slug, product.id);

  return product.id;
}
