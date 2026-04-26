export type StorePage = {
  slug: string;
  title: string;
  group: "corporate" | "legal" | "support";
  body: string;
  seo: {
    title: string;
    description: string;
  };
};

export type StoreCampaign = {
  slug: string;
  title: string;
  description: string;
  discount: string;
};

export const storePages: StorePage[] = [
  {
    slug: "hakkimizda",
    title: "Hakkımızda",
    group: "corporate",
    body: "Karacabey Gross Market, Karacabey ve çevresi için hızlı market siparişi, güvenli PayTR ödeme ve düzenli teslimat deneyimi sunar.",
    seo: {
      title: "Hakkımızda | Karacabey Gross Market",
      description: "Karacabey Gross Market kurumsal bilgileri, hizmet yaklaşımı ve online market altyapısı.",
    },
  },
  {
    slug: "iletisim",
    title: "İletişim",
    group: "corporate",
    body: "Sipariş, teslimat ve destek talepleriniz için Karacabey Gross Market müşteri hizmetleriyle iletişime geçebilirsiniz.",
    seo: {
      title: "İletişim | Karacabey Gross Market",
      description: "Karacabey Gross Market iletişim ve destek kanalları.",
    },
  },
  {
    slug: "kvkk",
    title: "KVKK",
    group: "legal",
    body: "Kişisel verileriniz, yürürlükteki mevzuata uygun şekilde işlenir, saklanır ve korunur.",
    seo: {
      title: "KVKK | Karacabey Gross Market",
      description: "Karacabey Gross Market KVKK aydınlatma metni.",
    },
  },
  {
    slug: "gizlilik-politikasi",
    title: "Gizlilik Politikası",
    group: "legal",
    body: "Web sitesi, mobil uygulama ve ödeme süreçlerindeki gizlilik yaklaşımımız bu sayfada özetlenir.",
    seo: {
      title: "Gizlilik Politikası | Karacabey Gross Market",
      description: "Karacabey Gross Market gizlilik politikası.",
    },
  },
  {
    slug: "mesafeli-satis-sozlesmesi",
    title: "Mesafeli Satış Sözleşmesi",
    group: "legal",
    body: "Online siparişleriniz mesafeli satış mevzuatı ve ilgili tüketici hakları kapsamında yürütülür.",
    seo: {
      title: "Mesafeli Satış Sözleşmesi | Karacabey Gross Market",
      description: "Karacabey Gross Market mesafeli satış sözleşmesi.",
    },
  },
  {
    slug: "iade-ve-degisim",
    title: "İade ve Değişim",
    group: "support",
    body: "İade ve değişim talepleri sipariş detayları, ödeme durumu ve ürün koşullarına göre değerlendirilir.",
    seo: {
      title: "İade ve Değişim | Karacabey Gross Market",
      description: "Karacabey Gross Market iade ve değişim süreçleri.",
    },
  },
  {
    slug: "sss",
    title: "SSS",
    group: "support",
    body: "Teslimat, ödeme, kupon ve hesap işlemleriyle ilgili sık sorulan sorular burada yer alır.",
    seo: {
      title: "SSS | Karacabey Gross Market",
      description: "Karacabey Gross Market sık sorulan sorular.",
    },
  },
];

export const storeCampaigns: StoreCampaign[] = [
  {
    slug: "haftalik-gross-firsatlari",
    title: "Haftalık Gross Fırsatları",
    description: "Temel gıda ve günlük ürünlerde avantajlı sepetler.",
    discount: "KGM25 kuponuyla 250 TL ve üzeri siparişlerde 25 TL indirim.",
  },
];

export function findStorePage(slug: string) {
  return storePages.find((page) => page.slug === slug);
}
