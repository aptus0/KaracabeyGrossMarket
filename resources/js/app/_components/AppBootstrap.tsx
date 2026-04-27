"use client";

import { useEffect, useRef } from "react";
import { useAuthStore } from "@/lib/auth-store";
import { useCartStore } from "@/lib/cart-store";

export function AppBootstrap() {
  const authHydrated = useAuthStore((state) => state.isHydrated);
  const authToken = useAuthStore((state) => state.token);
  const initializeAuth = useAuthStore((state) => state.initialize);
  const initializeCart = useCartStore((state) => state.initialize);
  const bootedRef = useRef(false);

  useEffect(() => {
    if (!authHydrated) {
      return;
    }

    initializeAuth()
      .catch(() => undefined)
      .finally(() => {
        initializeCart().catch(() => undefined);
        bootedRef.current = true;
      });
  }, [authHydrated, initializeAuth, initializeCart]);

  useEffect(() => {
    if (!authHydrated || !bootedRef.current) {
      return;
    }

    initializeCart({ silent: true }).catch(() => undefined);
  }, [authHydrated, authToken, initializeCart]);

  return null;
}
