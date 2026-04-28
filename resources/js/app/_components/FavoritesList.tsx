"use client";

import { useEffect, useState } from "react";
import Image from "next/image";
import Link from "next/link";
import { Heart, Loader2 } from "lucide-react";
import { fetchUserFavorites, formatCartMoney, type FavoriteProduct } from "@/lib/account";
import { useAuthStore } from "@/lib/auth-store";

export function FavoritesList() {
  const token = useAuthStore((state) => state.token);
  const isHydrated = useAuthStore((state) => state.isHydrated);
  const [favorites, setFavorites] = useState<FavoriteProduct[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!isHydrated) return;
    if (!token) {
      setLoading(false);
      return;
    }

    fetchUserFavorites(token)
      .then(setFavorites)
      .catch(() => setError("Favoriler yüklenemedi."))
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

  if (favorites.length === 0) {
    return (
      <div className="flex flex-col items-center gap-3 py-10 text-center text-[#9AA3AF]">
        <Heart size={36} />
        <p className="text-sm">Favori ürününüz bulunmuyor.</p>
        <Link href="/products" className="secondary-action text-xs">
          Ürünlere Gözat
        </Link>
      </div>
    );
  }

  return (
    <div className="product-grid">
      {favorites.map((product) => (
        <Link
          key={product.id}
          href={`/product/${product.slug}`}
          className="group info-card flex flex-col gap-3 transition hover:shadow-md"
        >
          <div className="relative aspect-square overflow-hidden rounded-2xl border border-[#EEF1F4] bg-[#FAFBFC]">
            {product.image_url ? (
              <Image
                src={product.image_url}
                alt={product.name}
                fill
                sizes="(max-width: 640px) 100vw, 300px"
                className="object-cover transition group-hover:scale-105"
              />
            ) : (
              <div className="flex h-full items-center justify-center text-xs font-black text-[#9AA3AF]">
                KGM
              </div>
            )}
          </div>
          <div>
            <p className="text-xs font-semibold text-[#9AA3AF]">
              {product.brand ?? "Karacabey Gross Market"}
            </p>
            <strong className="line-clamp-2 text-sm font-black text-[#2B2F36]">
              {product.name}
            </strong>
            <p className="mt-1 text-sm font-black text-[#FF7A00]">
              {formatCartMoney(product.price_cents)}
            </p>
          </div>
        </Link>
      ))}
    </div>
  );
}
