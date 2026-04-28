"use client";

import { useEffect, useState } from "react";
import { Loader2, PackageX } from "lucide-react";
import { OrderCard } from "@/app/_components/OrderCard";
import { fetchUserOrders, type UserOrder } from "@/lib/account";
import { useAuthStore } from "@/lib/auth-store";

export function AccountOrders() {
  const token = useAuthStore((state) => state.token);
  const isHydrated = useAuthStore((state) => state.isHydrated);
  const [orders, setOrders] = useState<UserOrder[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!isHydrated) return;
    if (!token) {
      setLoading(false);
      return;
    }

    fetchUserOrders(token)
      .then((res) => setOrders(res.data ?? []))
      .catch(() => setError("Siparişler yüklenemedi."))
      .finally(() => setLoading(false));
  }, [token, isHydrated]);

  if (loading) {
    return (
      <div className="flex items-center justify-center py-10 text-[#9AA3AF]">
        <Loader2 size={22} className="animate-spin" />
      </div>
    );
  }

  if (error) {
    return <p className="py-4 text-sm text-[#DC2626]">{error}</p>;
  }

  if (orders.length === 0) {
    return (
      <div className="flex flex-col items-center gap-3 py-10 text-center text-[#9AA3AF]">
        <PackageX size={36} />
        <p className="text-sm">Henüz siparişiniz bulunmuyor.</p>
      </div>
    );
  }

  return (
    <div className="info-grid">
      {orders.map((order) => (
        <OrderCard key={order.id} order={order} />
      ))}
    </div>
  );
}
