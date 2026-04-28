"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { LogOut, Package, MapPin, RefreshCw, ShoppingBag } from "lucide-react";
import { AddressCard } from "@/app/_components/AddressCard";
import { OrderCard } from "@/app/_components/OrderCard";
import { AppLayout } from "@/app/_layouts/AppLayout";
import { useAuthStore } from "@/lib/auth-store";
import {
  deleteUserAddress,
  fetchUserAddresses,
  fetchUserOrders,
  type UserAddress,
  type UserOrder,
} from "@/lib/account";
import { useRouter } from "next/navigation";

// ─── Skeleton Loader ──────────────────────────────────────────────────────────

function SkeletonCard() {
  return (
    <div className="info-card animate-pulse">
      <div className="h-4 w-2/3 rounded-lg bg-[#F3F4F6]" />
      <div className="mt-3 h-3 w-1/2 rounded-lg bg-[#F3F4F6]" />
      <div className="mt-2 h-3 w-3/4 rounded-lg bg-[#F3F4F6]" />
    </div>
  );
}

// ─── AccountExperience ────────────────────────────────────────────────────────

export function AccountExperience() {
  const router = useRouter();
  const token = useAuthStore((state) => state.token);
  const user = useAuthStore((state) => state.user);
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
  const clearSession = useAuthStore((state) => state.clearSession);

  const [orders, setOrders] = useState<UserOrder[]>([]);
  const [addresses, setAddresses] = useState<UserAddress[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // Unauthenticated guard
  useEffect(() => {
    if (!isAuthenticated) {
      router.replace("/auth/login");
    }
  }, [isAuthenticated, router]);

  // Veri çekimi
  useEffect(() => {
    if (!token) return;

    let active = true;
    setLoading(true);
    setError(null);

    Promise.all([
      fetchUserOrders(token),
      fetchUserAddresses(token),
    ])
      .then(([ordersRes, addrsRes]) => {
        if (!active) return;
        setOrders(ordersRes.data ?? []);
        setAddresses(addrsRes);
      })
      .catch(() => {
        if (!active) return;
        setError("Hesap bilgileri yüklenemedi. Lütfen tekrar deneyin.");
      })
      .finally(() => {
        if (!active) return;
        setLoading(false);
      });

    return () => { active = false; };
  }, [token]);

  async function handleDeleteAddress(id: number) {
    if (!token) return;
    try {
      await deleteUserAddress(token, id);
      setAddresses((prev) => prev.filter((a) => a.id !== id));
    } catch {
      // sessizce devam
    }
  }

  function handleLogout() {
    clearSession();
    router.replace("/");
  }

  if (!isAuthenticated) return null;

  return (
    <AppLayout sidebar>
      {/* Başlık */}
      <section className="account-heading">
        <div>
          <p className="eyebrow">Müşteri paneli</p>
          <h1>Hesabım</h1>
          {user?.name ? (
            <p className="mt-1 text-sm text-[#6B7177]">
              Hoş geldiniz, <strong>{user.name}</strong>
            </p>
          ) : null}
        </div>
        <div className="flex flex-wrap gap-3">
          <Link className="secondary-action" href="/products">
            <ShoppingBag size={16} />
            Alışverişe Dön
          </Link>
          <button
            type="button"
            onClick={handleLogout}
            className="secondary-action flex items-center gap-2 border-[#FCA5A5] text-[#DC2626] hover:bg-[#FEF2F2]"
          >
            <LogOut size={16} />
            Çıkış Yap
          </button>
        </div>
      </section>

      {/* Özet istatistikler */}
      {!loading && (
        <section className="info-grid" aria-label="Hesap özeti">
          <article className="info-card">
            <strong>Toplam Sipariş</strong>
            <p className="mt-1 text-2xl font-black text-[#FF7A00]">{orders.length}</p>
            <p className="text-sm text-[#6B7177]">sipariş kaydı</p>
          </article>
          <article className="info-card">
            <strong>Kayıtlı Adres</strong>
            <p className="mt-1 text-2xl font-black text-[#FF7A00]">{addresses.length}</p>
            <p className="text-sm text-[#6B7177]">teslimat adresi</p>
          </article>
          <article className="info-card">
            <strong>Hesap</strong>
            <p className="mt-1 truncate text-sm font-semibold text-[#2B2F36]">{user?.email}</p>
            <p className="text-sm text-[#6B7177]">kayıtlı e-posta</p>
          </article>
        </section>
      )}

      {/* Hata durumu */}
      {error && (
        <div className="flex items-center justify-between gap-4 rounded-2xl border border-[#FCA5A5] bg-[#FEF2F2] p-4 text-sm font-semibold text-[#DC2626]">
          <span>{error}</span>
          <button
            type="button"
            onClick={() => { setError(null); setLoading(true); }}
            className="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-[#DC2626] hover:underline"
          >
            <RefreshCw size={13} /> Tekrar dene
          </button>
        </div>
      )}

      {/* Siparişler */}
      <section className="content-band content-band--narrow" id="orders">
        <div className="section-heading">
          <div>
            <p className="eyebrow">Siparişlerim</p>
            <h2>Son siparişler</h2>
          </div>
          {!loading && orders.length > 0 && (
            <span className="text-sm font-semibold text-[#6B7177]">{orders.length} sipariş</span>
          )}
        </div>

        {loading ? (
          <div className="info-grid">
            <SkeletonCard /><SkeletonCard /><SkeletonCard />
          </div>
        ) : orders.length === 0 ? (
          <div className="info-card flex flex-col items-center gap-3 py-8 text-center">
            <Package size={32} className="text-[#D1D5DB]" />
            <p className="font-semibold text-[#6B7177]">Henüz siparişiniz bulunmuyor.</p>
            <Link className="primary-action" href="/products">Alışverişe Başla</Link>
          </div>
        ) : (
          <div className="info-grid">
            {orders.map((order) => (
              <OrderCard key={order.id} order={order} />
            ))}
          </div>
        )}
      </section>

      {/* Adresler */}
      <section className="content-band content-band--narrow" id="addresses">
        <div className="section-heading">
          <div>
            <p className="eyebrow">Adreslerim</p>
            <h2>Teslimat adresleri</h2>
          </div>
        </div>

        {loading ? (
          <div className="info-grid">
            <SkeletonCard /><SkeletonCard />
          </div>
        ) : addresses.length === 0 ? (
          <div className="info-card flex flex-col items-center gap-3 py-8 text-center">
            <MapPin size={32} className="text-[#D1D5DB]" />
            <p className="font-semibold text-[#6B7177]">Kayıtlı adresiniz bulunmuyor.</p>
          </div>
        ) : (
          <div className="info-grid">
            {addresses.map((address) => (
              <AddressCard key={address.id} address={address} onDelete={handleDeleteAddress} />
            ))}
          </div>
        )}
      </section>
    </AppLayout>
  );
}
