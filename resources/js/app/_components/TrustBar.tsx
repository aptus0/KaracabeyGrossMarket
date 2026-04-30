import { Clock, CreditCard, MapPin, RotateCcw } from "lucide-react";

const trustItems = [
  {
    Icon: Clock,
    title: "Aynı Gün Teslimat",
    description: "Karacabey içi siparişler",
    color: "trust-bar__item--orange",
  },
  {
    Icon: CreditCard,
    title: "Güvenli Ödeme",
    description: "256-bit SSL koruması",
    color: "trust-bar__item--blue",
  },
  {
    Icon: MapPin,
    title: "Yerel Stok",
    description: "Taze ve yerel ürünler",
    color: "trust-bar__item--green",
  },
  {
    Icon: RotateCcw,
    title: "Kolay İade",
    description: "7 gün iade garantisi",
    color: "trust-bar__item--purple",
  },
];

export function TrustBar() {
  return (
    <section className="trust-bar-wrapper" aria-label="Alışveriş güvencelerimiz">
      <div className="content-band trust-bar-band">
        <ul className="trust-bar" role="list">
          {trustItems.map((item) => {
            const Icon = item.Icon;
            return (
              <li key={item.title} className={`trust-bar__item ${item.color}`}>
                <span className="trust-bar__icon" aria-hidden="true">
                  <Icon size={20} />
                </span>
                <span className="trust-bar__copy">
                  <strong>{item.title}</strong>
                  <small>{item.description}</small>
                </span>
              </li>
            );
          })}
        </ul>
      </div>
    </section>
  );
}
