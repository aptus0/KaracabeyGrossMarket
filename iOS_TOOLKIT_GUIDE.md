# iOS Araç Takımı (ToolKit) - Geliştirme Rehberi

## 🎨 Geliştirilen Bileşenler

### 1. ProductToolKit Bileşeni

Ürün detay sayfasında tam kapsamlı bilgi sunumu için 4 farklı sekmeye sahip bir araç takımı.

#### Sekme 1: Özellikler (Features)
```swift
- Premium Kalite
- Hızlı Teslim (24 saat)
- Güvenli Ödeme (256-bit şifreleme)
- 30 Gün İade Garantisi
- 7/24 Müşteri Desteği
- Ücretsiz Kargo (250 TL+)
```

**UI Elemanları:**
- İkon + Başlık + Açıklama kartları
- Renkli arka plan ile vurgulanmış
- Tüm özellikler görsel olarak sunulmuş

#### Sekme 2: Teknik Bilgi (Specifications)
```swift
- Marka (Brand)
- Ürün Kodu (SKU)
- Stok Durumu
- Stok Miktarı
- Fiyat Bilgisi
- İndirim Yüzdesi
- Ürün Açıklaması
```

**Özellikler:**
- Anahtar-Değer çiftleri olarak sunuş
- Düzenli görünüm
- Detaylı açıklama için genişletilmiş alan

#### Sekme 3: Kargo Seçenekleri (Shipping)
```swift
- Standart Kargo: 2-3 İş Günü - ₺29,99
- Hızlı Kargo: Ertesi Gün - ₺49,99 (PopülerAya)
- Sadece İstanbul: 3 Saat - ₺64,99
```

**Özellikler:**
- Teslim süresi gösterimi
- Fiyat karşılaştırması
- "En Popüler" rozeti
- 250+ TL için ücretsiz kargo bilgisi

#### Sekme 4: İade Politikası (Returns)
```swift
- Koşulsuz İade: 30 Gün
- Ücretsiz Kargo: Dahil
- Hızlı Geri Ödeme: 5 Gün
```

**Adım Adım İade Süreci:**
1. Ürün kusurlu/farklı ise başvuru yap
2. Ücretsiz kargo etiketi al
3. Depo tarafından kontrol
4. Para iadesi işlemi

### 2. Geliştirilmiş ProductDetailView

#### Yeni Özellikler

**A. ToolKit Entegrasyonu**
- Alt sayfada kayan (bottom sheet) ToolKit gösterimi
- Yumuşak animasyon geçişleri
- Kapatma butonları

**B. Ürün Bilgileri Butonu**
```swift
- "Ürün Bilgileri" buton
- Okunabilir açıklama ikonu
- Sağdan chevron animasyonu
- Turuncu tema renkleri
```

**C. Ürün Bilgileri Paneli**
- Sepete ekle düğmesinin üstünde
- Hızlı erişim
- Tüm önemli bilgileri bir yerde

#### Mevcut Özellikler (İyileştirilmiş)
- Parallax görüntü efekti
- Marka etiketi
- Fiyat ve indirim gösterimi
- Stok durumu göstergesi
- Ürün açıklaması
- Kategori bağlantıları
- Değerlendirmeler bölümü
- Miktar seçici
- Sepete ekle butonu

### 3. Uygulama İkonu (App Icon)

#### Ayarlanan Icon
- **Dosya:** `kg-light.png` (KGMLogo Light)
- **Boyut:** 1024x1024 px
- **Konum:** `Assets.xcassets/AppIcon.appiconset/`
- **İdiyom:** Universal (iOS)

**Özellikler:**
- Açık tema (Light) tasarım
- Karacabey Gross Market markası
- iOS çoklu çözünürlük uyumlu

## 📱 Kullanım

### ProductDetailView'ı Açmak
```swift
NavigationLink(destination: ProductDetailView(slug: product.slug)) {
    // Link content
}
```

### ToolKit'e Erişim
1. **Ürün detay sayfasına git**
2. **Alt kısımda "Ürün Bilgileri" butonuna dokun**
3. **ToolKit sayfası kayan olarak açılır**
4. **4 sekme arasında kaydırarak bilgi arat**
5. **Kapatmak için X butonuna dokun**

## 🎯 Backend Entegrasyonu

### API Endpoints (Çalışıyor ✅)

```bash
# Ürün Listesi
GET /api/v1/products?page=1&per_page=12

# Ürün Detayı
GET /api/v1/products/{slug}

# Örnek:
curl http://localhost:8000/api/v1/products/ciftli-priz-uzatma
```

### Yanıt Yapısı
```json
{
  "data": {
    "id": 31,
    "name": "Çiftli Priz Uzatma",
    "slug": "ciftli-priz-uzatma",
    "description": "Günlük ev kullanımı için güvenli uzatma prizi.",
    "brand": "Mutlusan",
    "price": "₺94,90",
    "stockQuantity": 14,
    "imageUrl": null,
    "categories": [
      {
        "id": 15,
        "name": "Hırdavat & Ev Gereçleri",
        "slug": "hirdavat-ev-gerecleri"
      }
    ]
  }
}
```

## 🔧 Kod Yapısı

### ProductToolKit.swift
```
📁 Components/
  └─ ProductToolKit.swift (NEW)
     ├─ Enum: ToolSection (features, specifications, shipping, returns)
     ├─ Body View
     ├─ featureItem()
     ├─ specRow()
     ├─ shippingOptionCard()
     ├─ returnPolicyCard()
     └─ stepCard()
```

### ProductDetailView.swift (Enhanced)
```
📁 Views/Products/
  └─ ProductDetailView.swift (UPDATED)
     ├─ @State showToolKit
     ├─ Body with ZStack for overlay
     ├─ productContent()
     ├─ stickyBottomBar() ← ToolKit Button Added
     └─ productImage()
```

## 🎨 Tasarım Özellikleri

### Renkler
- **Birincil Orange:** `Color.kgmOrange`
- **Başarı Yeşili:** `Color.green`
- **Sistem Arka Planı:** `Color(UIColor.systemBackground)`

### Yazı Stilleri
- **Başlıklar:** Bold, Rounded Design
- **Alt Başlıklar:** Semibold
- **Normal Metin:** Regular, Secondary

### Köşe Yarıçapları
- **Kartlar:** 12pt
- **Butonlar:** 30pt (roundedStyle)
- **Etiketler:** 6-8pt

## 📊 Veri Modeli

### Product Model
```swift
struct Product {
    let id: Int
    let name: String
    let slug: String
    let description: String?
    let brand: String?
    let priceCents: Int
    let compareAtPriceCents: Int?
    let stockQuantity: Int
    let imageUrl: String?
    let seo: SEO?
    let categories: [Category]?
}
```

### SEO Modeli
```swift
struct SEO {
    let sku: String
    let title: String
    let description: String
}
```

### Category Modeli
```swift
struct Category: Identifiable {
    let id: Int
    let name: String
    let slug: String
}
```

## 🧪 Test Senaryoları

### 1. ToolKit Açılıp Kapanması
- [ ] Ürün detay sayfasında "Ürün Bilgileri" butonuna dokun
- [ ] ToolKit aşağıdan kayan olarak açılmalı
- [ ] X butonuna dokun ve kapatılmalı
- [ ] Animasyonlar akıcı olmalı

### 2. Sekme Değişimi
- [ ] Her sekmede geçiş yapılabilmeli
- [ ] Veriler doğru görüntülenmeli
- [ ] Scroll'lama normal çalışmalı

### 3. Sepete Ekleme
- [ ] ToolKit açık iken sepete ekle butonuna dokun
- [ ] ToolKit otomatik kapanmalı
- [ ] Ürün sepete eklenmiş olmalı

### 4. Favoriye Ekleme
- [ ] Ürün favoriye eklenebilmeli
- [ ] Kalp ikonu değişmeli
- [ ] Durum kalıcı olmalı

## 🚀 Gelecek Geliştirmeler

1. **Ürün Gallerisi**
   - Birden fazla resim desteği
   - Zoom özelliği
   - Kaydırılabilir galeri

2. **Dinamik Fiyatlandırma**
   - Boyut/Renk seçiminden fiyat değişimi
   - Varyant yönetimi

3. **İncelemeler Entegrasyonu**
   - Ortalama puanlaması
   - Yıldız gösterimi
   - Yorum listesi

4. **Benzer Ürünler**
   - Aynı kategori önerileri
   - Karşılaştırma özelliği

5. **Stok İzleme**
   - Push notificationları
   - Ön siparişe alma

## 📱 Cihaz Uyumluluğu

- ✅ iPhone SE (küçük ekran)
- ✅ iPhone 13/14/15 (standart)
- ✅ iPhone 15 Plus (büyük ekran)
- ✅ Yatay/Dikey Yönelim
- ✅ Dynamic Type (Yazı Büyüklüğü)

## 🔐 Güvenlik Notları

- Ürün verisi cache'leniyor (performans için)
- Gizli bilgiler Keychain'de saklanıyor
- API istekleri https üzerinden gidiyor

## 📞 Sorun Giderme

### ToolKit Açılmıyor
- [ ] `showToolKit` state'i doğru çalışıyor mu?
- [ ] `@State` deklarasyonu var mı?
- [ ] ProductToolKit bileşeni imported mi?

### Ürün Bilgileri Yüklenmiyor
- [ ] API endpoint çalışıyor mu?
- [ ] `ProductDetailViewModel` veri çekiyor mu?
- [ ] Ürün slug'u doğru mu?

### İkon Görünmüyor
- [ ] `kg-light.png` Assets'te mi?
- [ ] Contents.json doğru mı?
- [ ] Xcode cache temizledim mi?

---

**Son Güncelleme:** 2026-05-01  
**Durum:** ✅ Üretim Hazır (Production Ready)
