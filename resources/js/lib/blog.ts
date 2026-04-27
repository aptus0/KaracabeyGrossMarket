export type BlogPost = {
  slug: string;
  title: string;
  excerpt: string;
  category: string;
  publishedAt: string;
  readTime: string;
  heroImage: string;
  content: string[];
  seo: {
    title: string;
    description: string;
  };
};

export const blogPosts: BlogPost[] = [
  {
    slug: "karacabey-gross-alisveris-listesi-nasil-planlanir",
    title: "Karacabey gross alışveriş listesi nasıl daha verimli planlanır?",
    excerpt: "Haftalık siparişleri daha az tekrar, daha güçlü stok kontrolü ve daha net bütçe ile yönetmek için pratik bir çerçeve.",
    category: "Sipariş Planlama",
    publishedAt: "2026-04-27",
    readTime: "4 dk",
    heroImage: "https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1400&q=80",
    content: [
      "Haftalık market planı hazırlanırken ilk adım tüketim ritmini doğru okumaktır. Süt, kahvaltılık, temizlik ve hızlı dönen temel gıda kalemleri aynı torbaya atıldığında siparişler ya gereğinden sıklaşır ya da son gün sürpriz stok açıkları oluşur.",
      "En pratik yöntem, ürünleri teslimat hassasiyetine göre katmanlamaktır. Soğuk zincir ürünler, taze ürünler ve uzun ömürlü stok kalemleri ayrı gruplandığında checkout anı daha kontrollü hale gelir.",
      "Kurumsal veya yoğun aile alışverişlerinde favori ürün listesini canlı sepetle birlikte kullanmak ciddi zaman kazandırır. Aynı ürünler tekrar tekrar aranmaz, yalnız eksik kalanlar güncellenir.",
    ],
    seo: {
      title: "Karacabey gross alışveriş listesi nasıl planlanır? | Karacabey Gross Market",
      description: "Haftalık market listesini daha hızlı, dengeli ve kontrollü hazırlamak için uygulanabilir gross sipariş önerileri.",
    },
  },
  {
    slug: "hizli-teslimat-icin-adres-akisi",
    title: "Hızlı teslimat için adres akışını neden önceden düzenlemek gerekir?",
    excerpt: "Adreslerin doğru etiketlenmesi, teslimat notlarının kısa tutulması ve checkout sırasında zaman kaybının azaltılması için öneriler.",
    category: "Teslimat Deneyimi",
    publishedAt: "2026-04-24",
    readTime: "3 dk",
    heroImage: "https://images.unsplash.com/photo-1520607162513-77705c0f0d4a?auto=format&fit=crop&w=1400&q=80",
    content: [
      "Teslimat akışında en sık yaşanan gecikmelerin önemli bir kısmı yanlış değil, eksik adres bilgisinden doğar. Kat, blok, işletme adı veya giriş talimatı net olmadığında saha ekibi ikinci bir doğrulama yapmak zorunda kalır.",
      "Bu yüzden ev ve iş adreslerini ayrı başlıklarla kaydetmek, her biri için kısa ama tamamlayıcı teslimat notu bırakmak iyi bir alışkanlıktır. Mobilde yapılan her küçük düzenleme, sipariş gününde büyük bir akıcılık sağlar.",
      "Adres akışı sadece lojistik verim için değil, müşteri deneyimi için de kritik bir yüzeydir. Özellikle tekrar sipariş veren kullanıcıda checkout süresi ne kadar kısalırsa sepet tamamlama oranı o kadar yükselir.",
    ],
    seo: {
      title: "Hızlı teslimat için adres akışı | Karacabey Gross Market",
      description: "Teslimat adreslerini daha doğru ve hızlı sipariş deneyimi için nasıl düzenlemek gerektiğini anlatan kısa rehber.",
    },
  },
  {
    slug: "online-market-odemede-guven-sinyalleri",
    title: "Online market ödemesinde hangi güven sinyalleri gerçekten önemlidir?",
    excerpt: "SSL, 3D Secure, sağlayıcı doğrulama katmanı ve ödeme sayfası davranışlarını sade bir dille açıklayan rehber.",
    category: "Ödeme Güvenliği",
    publishedAt: "2026-04-20",
    readTime: "5 dk",
    heroImage: "https://images.unsplash.com/photo-1556740749-887f6717d7e4?auto=format&fit=crop&w=1400&q=80",
    content: [
      "Kullanıcı tarafında güven hissi çoğu zaman tek bir ibareye değil, küçük işaretlerin toplamına dayanır. SSL, 3D Secure, bilinen ödeme sağlayıcısı kullanımı ve şeffaf sipariş özeti bu işaretlerin başında gelir.",
      "Profesyonel bir checkout yüzeyi, kullanıcıyı gereksiz bilgiyle yormadan kritik sinyalleri görünür kılar. Toplam tutar, teslimat bilgisi, sepet özeti ve ödeme yönlendirmesi ne kadar açık olursa tereddüt o kadar azalır.",
      "Ödeme deneyimi güven verdiğinde sadece ilk sipariş değil, tekrar sipariş olasılığı da güçlenir. Bu nedenle footer ve checkout yüzeylerindeki güven katmanı doğrudan dönüşüm kalitesiyle ilişkilidir.",
    ],
    seo: {
      title: "Online market ödemede güven sinyalleri | Karacabey Gross Market",
      description: "SSL, 3D Secure ve güvenli ödeme akışında gerçekten önemli olan sinyalleri açıklayan içerik.",
    },
  },
  {
    slug: "favori-listesi-ile-tekrar-siparis",
    title: "Favori listesi ile tekrar sipariş süresi nasıl kısalır?",
    excerpt: "Tekrar alınan ürünlerin favorilere alınmasıyla mobilde daha kısa, daha düzenli ve daha tahmin edilebilir sipariş akışı kurulabilir.",
    category: "Mobil Deneyim",
    publishedAt: "2026-04-17",
    readTime: "4 dk",
    heroImage: "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1400&q=80",
    content: [
      "Mobil kullanıcılar uzun ürün aramalarından çok hızlı karar akışlarını sever. Bu yüzden tekrar alınan ürünlerin favorilere eklenmesi, yeni sipariş oluştururken ciddi zaman kazandırır.",
      "Favori listesi doğru kullanıldığında kullanıcı önce alışkanlık kalemlerini tamamlar, sonra yeni kampanya ve taze ürünlere yönelir. Böylece ana ihtiyaç listesi her siparişte sıfırdan kurulmaz.",
      "Canlı sepet ve alt app bar gibi yüzeylerle birleşen favori akışı, özellikle tek elde ve kısa sürede alışveriş yapan kullanıcı için daha rahat bir deneyim oluşturur.",
    ],
    seo: {
      title: "Favori listesi ile tekrar sipariş | Karacabey Gross Market",
      description: "Mobil kullanıcı için favori listesi ve canlı sepet birleştiğinde sipariş akışının nasıl hızlandığını anlatan yazı.",
    },
  },
];

export function findBlogPost(slug: string) {
  return blogPosts.find((post) => post.slug === slug);
}
