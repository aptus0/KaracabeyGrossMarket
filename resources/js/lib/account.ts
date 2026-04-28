"use client";

import { apiRequest } from "@/lib/api";
import { formatCartMoney } from "@/lib/cart";

export type UserAddress = {
  id: number;
  title: string;
  recipient_name: string;
  phone: string;
  city: string;
  district: string;
  neighborhood?: string | null;
  address_line: string;
  postal_code?: string | null;
  is_default: boolean;
};

export type UserOrderItem = {
  id: number;
  name: string;
  quantity: number;
  unit_price_cents: number;
  line_total_cents: number;
};

export type UserOrder = {
  id: number;
  merchant_oid: string;
  checkout_ref: string;
  status: string;
  status_label: string;
  currency: string;
  subtotal_cents: number;
  shipping_cents: number;
  discount_cents: number;
  total_cents: number;
  customer_name: string;
  customer_email: string;
  customer_phone: string;
  shipping_city?: string | null;
  shipping_district?: string | null;
  shipping_address: string;
  paid_at?: string | null;
  created_at: string;
  items: UserOrderItem[];
};

export type FavoriteProduct = {
  id: number;
  name: string;
  slug: string;
  brand?: string | null;
  price_cents: number;
  price: string;
  image_url?: string | null;
};

type PaginatedResponse<T> = {
  data: T[];
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
};

function authHeaders(token: string) {
  return { Authorization: `Bearer ${token}` };
}

export async function fetchUserOrders(token: string): Promise<PaginatedResponse<UserOrder>> {
  return apiRequest<PaginatedResponse<UserOrder>>("/api/v1/orders", {
    headers: authHeaders(token),
  });
}

export async function fetchUserOrder(token: string, orderId: number): Promise<UserOrder> {
  return apiRequest<UserOrder>(`/api/v1/orders/${orderId}`, {
    headers: authHeaders(token),
  });
}

export async function fetchUserAddresses(token: string): Promise<UserAddress[]> {
  const res = await apiRequest<UserAddress[]>("/api/v1/addresses", {
    headers: authHeaders(token),
  });
  return Array.isArray(res) ? res : (res as { data?: UserAddress[] }).data ?? [];
}

export async function deleteUserAddress(token: string, addressId: number): Promise<void> {
  await apiRequest(`/api/v1/addresses/${addressId}`, {
    method: "DELETE",
    headers: authHeaders(token),
  });
}

export async function fetchUserFavorites(token: string): Promise<FavoriteProduct[]> {
  const res = await apiRequest<FavoriteProduct[]>("/api/v1/favorites", {
    headers: authHeaders(token),
  });
  return Array.isArray(res) ? res : (res as { data?: FavoriteProduct[] }).data ?? [];
}

export function orderStatusColor(status: string): string {
  switch (status) {
    case "paid":
      return "text-[#16A34A]";
    case "awaiting_payment":
      return "text-[#D97706]";
    case "failed":
    case "cancelled":
      return "text-[#DC2626]";
    case "refunded":
      return "text-[#6B7177]";
    default:
      return "text-[#2B2F36]";
  }
}

export function formatOrderDate(iso: string): string {
  return new Intl.DateTimeFormat("tr-TR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  }).format(new Date(iso));
}

export { formatCartMoney };
