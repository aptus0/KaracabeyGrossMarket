import { buildApiUrl } from "@/lib/api";

export type NavigationItem = {
  id?: number;
  label: string;
  url: string;
  icon?: string | null;
  external?: boolean;
};

export type NavigationData = {
  top: NavigationItem[];
  header: NavigationItem[];
  category: NavigationItem[];
  footer_primary: NavigationItem[];
  footer_corporate: NavigationItem[];
  footer_support: NavigationItem[];
  footer_account: NavigationItem[];
};

export const defaultNavigation: NavigationData = {
  top: [
    { label: "Kargo Takip", url: "/cargo-tracking", icon: "package-search" },
    { label: "Adresim", url: "/addresses", icon: "map-pin" },
  ],
  header: [
    { label: "Ürünler", url: "/products", icon: "grid" },
    { label: "Kampanyalar", url: "/kampanyalar", icon: "tag" },
    { label: "Kurumsal", url: "/kurumsal/hakkimizda", icon: "file-text" },
  ],
  category: [
    { label: "Süt ve Kahvaltılık", url: "/products?category=sut-ve-kahvaltilik", icon: "grid" },
    { label: "Fırın", url: "/products?category=firin", icon: "grid" },
    { label: "Meyve Sebze", url: "/products?category=meyve-sebze", icon: "grid" },
    { label: "Temel Gıda", url: "/products?category=temel-gida", icon: "grid" },
    { label: "Tüm Ürünler", url: "/products", icon: "grid" },
  ],
  footer_primary: [
    { label: "Ürünler", url: "/products", icon: "grid" },
    { label: "Kampanyalar", url: "/kampanyalar", icon: "tag" },
    { label: "Sepet", url: "/checkout", icon: "cart" },
  ],
  footer_corporate: [
    { label: "Hakkımızda", url: "/kurumsal/hakkimizda", icon: "file-text" },
    { label: "İletişim", url: "/kurumsal/iletisim", icon: "phone" },
    { label: "KVKK", url: "/kurumsal/kvkk", icon: "shield" },
  ],
  footer_support: [
    { label: "İade ve Değişim", url: "/kurumsal/iade-ve-degisim", icon: "package-search" },
    { label: "SSS", url: "/kurumsal/sss", icon: "file-text" },
  ],
  footer_account: [
    { label: "Hesabım", url: "/account", icon: "user" },
    { label: "Favoriler", url: "/favorites", icon: "heart" },
  ],
};

type NavigationResponse = {
  data?: Partial<NavigationData>;
};

export async function fetchNavigation(signal?: AbortSignal): Promise<NavigationData> {
  const response = await fetch(buildApiUrl("/api/v1/content/navigation"), {
    headers: {
      Accept: "application/json",
    },
    signal,
  });

  if (!response.ok) {
    throw new Error("Navigation could not be loaded.");
  }

  const payload = (await response.json()) as NavigationResponse;

  return {
    ...defaultNavigation,
    ...payload.data,
  };
}
