"use client";

import { create } from "zustand";
import { createJSONStorage, persist } from "zustand/middleware";
import { apiRequest } from "@/lib/api";

export type AuthUser = {
  id: number;
  name: string;
  phone: string | null;
  email: string | null;
  avatar_url?: string | null;
  google_id?: string | null;
  facebook_id?: string | null;
  email_verified_at?: string | null;
};

type AuthState = {
  token: string | null;
  expiresAt: string | null;
  user: AuthUser | null;
  isHydrated: boolean;
  isAuthenticated: boolean;
  markHydrated: () => void;
  setSession: (token: string, user: AuthUser, expiresAt?: string | null) => void;
  clearSession: () => void;
  initialize: () => Promise<AuthUser | null>;
  logout: () => Promise<void>;
};

function isTokenExpired(expiresAt: string | null): boolean {
  if (!expiresAt) return false;
  const ts = Date.parse(expiresAt);
  return Number.isFinite(ts) && ts <= Date.now();
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      token: null,
      expiresAt: null,
      user: null,
      isHydrated: false,
      isAuthenticated: false,
      markHydrated: () => {
        const { token, expiresAt } = get();
        if (token && isTokenExpired(expiresAt)) {
          set({ token: null, expiresAt: null, user: null, isAuthenticated: false, isHydrated: true });
          return;
        }
        set({ isHydrated: true, isAuthenticated: Boolean(token) });
      },
      setSession: (token, user, expiresAt = null) =>
        set({
          token,
          expiresAt,
          user,
          isAuthenticated: true,
          isHydrated: true,
        }),
      clearSession: () =>
        set({
          token: null,
          expiresAt: null,
          user: null,
          isAuthenticated: false,
          isHydrated: true,
        }),
      initialize: async () => {
        const { token, expiresAt } = get();

        if (!token || isTokenExpired(expiresAt)) {
          set({
            token: null,
            expiresAt: null,
            user: null,
            isAuthenticated: false,
            isHydrated: true,
          });

          return null;
        }

        try {
          const user = await apiRequest<AuthUser>("/api/v1/auth/me", {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });

          set({
            token,
            expiresAt,
            user,
            isAuthenticated: true,
            isHydrated: true,
          });

          return user;
        } catch {
          get().clearSession();
          return null;
        }
      },
      logout: async () => {
        const token = get().token;

        if (token) {
          try {
            await apiRequest("/api/v1/auth/logout", {
              method: "POST",
              headers: {
                Authorization: `Bearer ${token}`,
              },
            });
          } catch {
            // Sunucu oturumu silinemese bile yerel oturumu temizliyoruz.
          }
        }

        get().clearSession();
      },
    }),
    {
      name: "kgm-auth-store",
      storage: createJSONStorage(() => localStorage),
      partialize: (state) => ({
        token: state.token,
        expiresAt: state.expiresAt,
        user: state.user,
      }),
      onRehydrateStorage: () => (state) => {
        state?.markHydrated();
      },
    },
  ),
);
