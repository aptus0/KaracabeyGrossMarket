import type { KgmAddress } from "@/lib/catalog";

type AddressCardProps = {
  address: KgmAddress;
};

export function AddressCard({ address }: AddressCardProps) {
  return (
    <article className="info-card">
      <strong>{address.title}</strong>
      <p>{address.recipient}</p>
      <p>{address.line}</p>
    </article>
  );
}
