"use client";

import { create } from "zustand";
import { createJSONStorage, persist } from "zustand/middleware";
import { apiRequest } from "@/lib/api";

export type AuthUser = {
  id: number;
  name: string;
  email: string;
  avatar_url?: string | null;
  google_id?: string | null;
  facebook_id?: string | null;
  email_verified_at?: string | null;
};

type AuthState = {
  token: string | null;
  user: AuthUser | null;
  isHydrated: boolean;
  isAuthenticated: boolean;
  markHydrated: () => void;
  setSession: (token: string, user: AuthUser) => void;
  clearSession: () => void;
  initialize: () => Promise<AuthUser | null>;
  logout: () => Promise<void>;
};

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      token: null,
      user: null,
      isHydrated: false,
      isAuthenticated: false,
      markHydrated: () => set({ isHydrated: true, isAuthenticated: Boolean(get().token) }),
      setSession: (token, user) =>
        set({
          token,
          user,
          isAuthenticated: true,
          isHydrated: true,
        }),
      clearSession: () =>
        set({
          token: null,
          user: null,
          isAuthenticated: false,
          isHydrated: true,
        }),
      initialize: async () => {
        const token = get().token;

        if (!token) {
          set({
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
        user: state.user,
      }),
      onRehydrateStorage: () => (state) => {
        state?.markHydrated();
      },
    },
  ),
);
