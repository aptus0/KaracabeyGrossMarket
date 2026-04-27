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
    keywords: string[];
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
      keywords: ["alışveriş listesi", "haftalık market planı", "Karacabey market", "gross sipariş"],
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
      keywords: ["teslimat adresi", "hızlı teslimat", "adres yönetimi", "online market teslimat"],
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
      keywords: ["ödeme güvenliği", "3D Secure", "SSL ödeme", "online market ödeme"],
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
      keywords: ["favori listesi", "tekrar sipariş", "mobil market", "hızlı sepet"],
    },
  },
  {
    slug: "meyve-sebze-siparisinde-tazelik-nasil-korunur",
    title: "Meyve sebze siparişinde tazelik algısını güçlendiren 5 küçük detay",
    excerpt: "Ürün seçimi, teslimat saatleri ve hızlı tüketim planı ile taze ürün siparişlerini daha kontrollü yönetmek mümkün.",
    category: "Taze Ürünler",
    publishedAt: "2026-04-14",
    readTime: "4 dk",
    heroImage: "https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1400&q=80",
    content: [
      "Taze ürün siparişlerinde memnuniyet çoğu zaman fiyatla değil, ürünün kapıya ulaştığı andaki canlı görünümüyle ölçülür. Bu yüzden sipariş saati, teslimat aralığı ve saklama planı birlikte düşünülmelidir.",
      "Meyve ve sebze alışverişinde hızlı tüketilecek ürünlerle birkaç gün dayanacak ürünleri ayırmak pratik bir yöntemdir. Böylece kullanıcı sepeti sadece indirim odaklı değil, kullanım sırasına göre de optimize etmiş olur.",
      "Mobil sipariş akışında kategori kartları ve net ürün görselleri, taze ürün tarafında karar süresini ciddi biçimde kısaltır. Aradaki bu küçük UX farkı, tekrar sipariş davranışına doğrudan yansır.",
    ],
    seo: {
      title: "Meyve sebze siparişinde tazelik nasıl korunur? | Karacabey Gross Market",
      description: "Taze ürün siparişlerinde seçim, teslimat ve saklama tarafında dikkat edilmesi gereken pratik noktalar.",
      keywords: ["meyve sebze siparişi", "taze ürünler", "market tazelik", "sebze teslimatı"],
    },
  },
  {
    slug: "mobilde-hizli-checkout-icin-3-adim",
    title: "Mobilde daha hızlı checkout için 3 basit düzenleme",
    excerpt: "Adres, sepet ve ödeme akışını sadeleştiren küçük dokunuşlarla mobil sipariş tamamlama süresi ciddi biçimde düşebilir.",
    category: "Mobil Deneyim",
    publishedAt: "2026-04-11",
    readTime: "3 dk",
    heroImage: "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1400&q=80",
    content: [
      "Mobilde checkout başarısı çoğu zaman hız hissiyle ilgilidir. Kullanıcı tek elde dolaşırken çok fazla alan kaplayan gereksiz bloklar gördüğünde işlem yarıda kalabilir.",
      "Bu yüzden kaydedilmiş adres, canlı sepet özeti ve tek dokunuşta görünen satın alma alanı, mobil uygulama hissi veren en önemli üç yüzeydir. Özellikle tekrar siparişte bu üçlü büyük fark yaratır.",
      "Ürün listeleme ekranının iki kolonlu, ürün detay ekranının ise daha odaklı olması; karar ve ödeme arasındaki geçişi hızlandırır. Sonuçta kullanıcı daha az düşünür, daha hızlı tamamlar.",
    ],
    seo: {
      title: "Mobilde hızlı checkout için 3 adım | Karacabey Gross Market",
      description: "Mobil sipariş akışında adres, sepet ve ödeme tarafını hızlandıran kullanıcı deneyimi önerileri.",
      keywords: ["mobil checkout", "hızlı ödeme", "mobil uygulama deneyimi", "sepet akışı"],
    },
  },
  {
    slug: "gross-alisveriste-aile-butcesi-nasil-korunur",
    title: "Gross alışverişte aile bütçesini korumak için hangi ürünler toplu alınmalı?",
    excerpt: "Toplu alışverişte gerçekten avantaj sağlayan kalemleri ayırmak bütçe kontrolünü kolaylaştırır ve israfı azaltır.",
    category: "Bütçe Yönetimi",
    publishedAt: "2026-04-08",
    readTime: "5 dk",
    heroImage: "https://images.unsplash.com/photo-1556740749-887f6717d7e4?auto=format&fit=crop&w=1400&q=80",
    content: [
      "Her ürün toplu alım için uygun değildir. Uzun ömürlü temel gıda, temizlik ve yoğun tüketilen kahvaltılık ürünler bütçe avantajı yaratırken kısa raf ömürlü ürünlerde aynı durum tersine dönebilir.",
      "Aile bütçesi için sağlıklı yöntem, yüksek dönüşlü ürünleri belirleyip bunları kampanya dönemlerinde daha yüksek adetle sepete eklemektir. Böylece depolama ve kullanım dengesi bozulmadan tasarruf elde edilir.",
      "Online markette eski fiyat, yeni fiyat ve birim bilgisi aynı kart üzerinde görünür olduğunda kullanıcı gerçek avantajı daha hızlı fark eder. Bu da kampanya performansını ve sepet büyüklüğünü destekler.",
    ],
    seo: {
      title: "Gross alışverişte aile bütçesi nasıl korunur? | Karacabey Gross Market",
      description: "Toplu alışverişte hangi ürünlerin gerçekten avantaj sağladığını ve bütçenin nasıl dengede tutulacağını anlatan rehber.",
      keywords: ["aile bütçesi", "toplu alışveriş", "gross market", "kampanyalı ürünler"],
    },
  },
  {
    slug: "kampanya-donemlerinde-sepeti-nasil-dengelemeli",
    title: "Kampanya dönemlerinde sepeti büyütmeden fırsatlardan nasıl yararlanılır?",
    excerpt: "İndirimli ürünleri gerçekten ihtiyaç listesiyle eşleştirmek, kampanya dönemlerinde kontrolsüz harcamayı önler.",
    category: "Kampanya Yönetimi",
    publishedAt: "2026-04-05",
    readTime: "4 dk",
    heroImage: "https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=1400&q=80",
    content: [
      "Kampanyalar doğru kullanıldığında bütçeyi rahatlatır; plansız kullanıldığında ise gereksiz ürün yükü oluşturur. Bu yüzden sepete ürün eklemeden önce ihtiyaç listesi ile kampanya yüzeyi arasında net bir eşleşme kurulmalıdır.",
      "Sepet içinde toplam tutar, indirim etkisi ve adet kontrolü görünür oldukça kullanıcı daha bilinçli hareket eder. Bu basit şeffaflık, satın alma sonrası pişmanlığı da azaltır.",
      "Mobil kullanıcı için kampanya akışının kısa, ürün detaylarının net ve satın alma butonunun görünür olması dönüşüm kadar memnuniyet için de önemlidir.",
    ],
    seo: {
      title: "Kampanya dönemlerinde sepet nasıl dengelenir? | Karacabey Gross Market",
      description: "İndirim dönemlerinde ihtiyaç dışı harcama yapmadan fırsatlardan yararlanmak için uygulanabilir sepet önerileri.",
      keywords: ["kampanya ürünleri", "indirimli market alışverişi", "sepet yönetimi", "alışveriş fırsatları"],
    },
  },
];

export function findBlogPost(slug: string) {
  return blogPosts.find((post) => post.slug === slug);
}
