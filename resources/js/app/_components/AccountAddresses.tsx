"use client";

import { useEffect, useState } from "react";
import { Loader2, MapPinOff } from "lucide-react";
import { AddressCard } from "@/app/_components/AddressCard";
import { deleteUserAddress, fetchUserAddresses, type UserAddress } from "@/lib/account";
import { useAuthStore } from "@/lib/auth-store";

export function AccountAddresses() {
  const token = useAuthStore((state) => state.token);
  const isHydrated = useAuthStore((state) => state.isHydrated);
  const [addresses, setAddresses] = useState<UserAddress[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!isHydrated) return;
    if (!token) {
      setLoading(false);
      return;
    }

    fetchUserAddresses(token)
      .then(setAddresses)
      .catch(() => setError("Adresler yüklenemedi."))
      .finally(() => setLoading(false));
  }, [token, isHydrated]);

  async function handleDelete(id: number) {
    if (!token) return;
    try {
      await deleteUserAddress(token, id);
      setAddresses((prev) => prev.filter((a) => a.id !== id));
    } catch {
      setError("Adres silinemedi.");
    }
  }

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

  if (addresses.length === 0) {
    return (
      <div className="flex flex-col items-center gap-3 py-10 text-center text-[#9AA3AF]">
        <MapPinOff size={36} />
        <p className="text-sm">Kayıtlı adresiniz bulunmuyor.</p>
      </div>
    );
  }

  return (
    <div className="info-grid">
      {addresses.map((address) => (
        <AddressCard key={address.id} address={address} onDelete={handleDelete} />
      ))}
    </div>
  );
}
