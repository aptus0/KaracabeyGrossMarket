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
  category: string;
};

export type KgmCategory = {
  slug: string;
  name: string;
  count: number;
};

export type KgmCartItem = KgmProduct & {
  quantity: number;
};

export type KgmAddress = {
  title: string;
  recipient: string;
  line: string;
};

export type KgmOrder = {
  number: string;
  status: string;
  total: number;
};

export const categories: KgmCategory[] = [
  { slug: "sut-ve-kahvaltilik", name: "Süt ve Kahvaltılık", count: 18 },
  { slug: "firin", name: "Fırın", count: 12 },
  { slug: "meyve-sebze", name: "Meyve Sebze", count: 34 },
  { slug: "temel-gida", name: "Temel Gıda", count: 46 },
];

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
    category: "sut-ve-kahvaltilik",
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
    category: "firin",
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
    category: "meyve-sebze",
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
    category: "temel-gida",
  },
];

export const cartPreview: KgmCartItem[] = products.slice(0, 3).map((product, index) => ({
  ...product,
  quantity: index + 1,
}));

export const accountAddresses: KgmAddress[] = [
  {
    title: "Ev",
    recipient: "Karacabey Gross Müşteri",
    line: "Karacabey merkez, Bursa",
  },
  {
    title: "İş",
    recipient: "Teslimat Noktası",
    line: "Karacabey sanayi bölgesi, Bursa",
  },
];

export const accountOrders: KgmOrder[] = [
  {
    number: "KGM260426A1",
    status: "Ödeme bekliyor",
    total: 184.2,
  },
  {
    number: "KGM260426A0",
    status: "Teslim edildi",
    total: 512.7,
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

export function filterProducts(category?: string | string[], query?: string | string[]) {
  const categoryValue = Array.isArray(category) ? category[0] : category;
  const queryValue = Array.isArray(query) ? query[0] : query;
  const normalizedQuery = queryValue?.toLocaleLowerCase("tr-TR").trim();

  return products.filter((product) => {
    const matchesCategory = categoryValue ? product.category === categoryValue : true;
    const matchesQuery = normalizedQuery
      ? [product.name, product.brand, product.description].some((value) =>
          value.toLocaleLowerCase("tr-TR").includes(normalizedQuery),
        )
      : true;

    return matchesCategory && matchesQuery;
  });
}
