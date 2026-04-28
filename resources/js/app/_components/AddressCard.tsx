import { MapPin } from "lucide-react";
import type { UserAddress } from "@/lib/account";

type AddressCardProps = {
  address: UserAddress;
  onDelete?: (id: number) => void;
};

export function AddressCard({ address, onDelete }: AddressCardProps) {
  return (
    <article className="info-card relative">
      {address.is_default ? (
        <span className="mb-2 inline-block rounded-full bg-[#FFF3E6] px-3 py-0.5 text-xs font-black uppercase tracking-widest text-[#FF7A00]">
          Varsayılan
        </span>
      ) : null}
      <div className="flex items-start gap-2">
        <MapPin size={16} className="mt-0.5 shrink-0 text-[#FF7A00]" />
        <div className="min-w-0">
          <strong className="block text-sm font-black text-[#2B2F36]">{address.title}</strong>
          <p className="mt-1 text-sm text-[#6B7177]">{address.recipient_name}</p>
          <p className="text-sm text-[#6B7177]">{address.phone}</p>
          <p className="mt-1 text-sm text-[#6B7177]">
            {[address.neighborhood, address.address_line].filter(Boolean).join(", ")}
          </p>
          <p className="text-sm text-[#6B7177]">
            {[address.district, address.city, address.postal_code].filter(Boolean).join(" / ")}
          </p>
        </div>
      </div>
      {onDelete ? (
        <button
          type="button"
          onClick={() => onDelete(address.id)}
          className="mt-3 text-xs font-semibold text-[#DC2626] underline-offset-2 hover:underline"
        >
          Sil
        </button>
      ) : null}
    </article>
  );
}
