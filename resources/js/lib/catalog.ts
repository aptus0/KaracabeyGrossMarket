export type KgmProduct = {
  slug: string;
  name: string;
  brand: string;
  price: number;
  oldPrice?: number;
  unit: string;
  stock: number;
  image: string;
  badge: string;
  description: string;
};

export const products: KgmProduct[] = [
  {
    slug: "gunluk-sut-1-l",
    name: "Günlük Süt 1 L",
    brand: "KGM",
    price: 44.9,
    unit: "1 L",
    stock: 120,
    image: "https://images.unsplash.com/photo-1550583724-b2692b85b150?auto=format&fit=crop&w=720&q=80",
    badge: "Soğuk zincir",
    description: "Günlük kahvaltı ve mutfak kullanımı için taze süt.",
  },
  {
    slug: "taze-ekmek",
    name: "Taze Ekmek",
    brand: "KGM Fırın",
    price: 12.5,
    unit: "adet",
    stock: 300,
    image: "https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=720&q=80",
    badge: "Günlük",
    description: "Sabah üretimi, çıtır kabuklu günlük ekmek.",
  },
  {
    slug: "karacabey-domates-1-kg",
    name: "Karacabey Domates 1 Kg",
    brand: "Yerel Üretici",
    price: 38.9,
    unit: "1 kg",
    stock: 80,
    image: "https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=720&q=80",
    badge: "Yerel",
    description: "Karacabey üreticilerinden seçilmiş taze domates.",
  },
  {
    slug: "aycicek-yagi-5-l",
    name: "Ayçiçek Yağı 5 L",
    brand: "Gross Seçim",
    price: 329.9,
    oldPrice: 349.9,
    unit: "5 L",
    stock: 45,
    image: "https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=720&q=80",
    badge: "Avantaj",
    description: "Aile mutfağı ve toplu alışveriş için ekonomik yağ.",
  },
];

export function formatPrice(value: number) {
  return new Intl.NumberFormat("tr-TR", {
    style: "currency",
    currency: "TRY",
  }).format(value);
}

export function findProduct(slug: string) {
  return products.find((product) => product.slug === slug);
}
