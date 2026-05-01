# iOS UI Bileşenleri - Görsel Referans Kılavuzu

## 📱 Ürün Detay Sayfası Mimarisi

```
┌─────────────────────────────────────┐
│         Navigation Bar              │
│  [Back]    Ürün Adı    [❤️] [🛒]   │
└─────────────────────────────────────┘
│                                     │
│      ┌───────────────────────┐      │
│      │                       │      │
│      │   Ürün Görüntüsü      │      │  350pt
│      │   (Parallax Efekt)    │      │
│      │                       │      │
│      └───────────────────────┘      │
│                                     │
├─────────────────────────────────────┤
│                                     │
│  [Brand] MUTLUSAN                   │
│                                     │
│  Çiftli Priz Uzatma                │
│                                     │
│  ₺94,90    [10% İNDİRİM]            │
│                                     │
├─────────────────────────────────────┤
│  🟢 Stokta Var                       │
│     Hemen teslimata uygun           │
│                                     │
├─────────────────────────────────────┤
│  Ürün Açıklaması                    │
│  Günlük ev kullanımı için güvenli   │
│  uzatma prizi. Yüksek kaliteli      │
│  malzeme ile yapılmıştır.           │
│                                     │
├─────────────────────────────────────┤
│  Kategoriler                        │
│  [Hırdavat & Ev Gereçleri]          │
│                                     │
├─────────────────────────────────────┤
│  Değerlendirmeler          [Tümü]   │
│  ⭐⭐⭐⭐☆  4.5 / 127 Değerlendirme  │
│  [Değerlendirme Yap]                │
│                                     │
├─────────────────────────────────────┤
│                                     │
│     [Ürün Bilgileri →]   BUTTON     │
│                                     │
│  [- 1 +]    [Sepete Ekle]  BUTTONS  │
│                                     │
└─────────────────────────────────────┘
```

---

## 🎨 ToolKit Panel Mimarisi

```
╔═════════════════════════════════════╗
║  Ürün Bilgileri            [X]      ║  <- Header
╠═════════════════════════════════════╣
║ [Özellikler] [Teknik] [Kargo] [İade]║  <- Segmented Picker
╠═════════════════════════════════════╣
║                                     ║
║  ScrollView İçeriği                 ║
║  ─────────────────────────────      ║
║                                     ║
║  🎯 Premium Kalite                  ║
║  Yüksek kaliteli malzeme            ║
║  ile üretilmiştir                   ║
║                                     ║
║  ⚡ Hızlı Teslim                    ║
║  24 saat içinde kargo çıkışı        ║
║                                     ║
║  🔒 Güvenli Ödeme                  ║
║  256-bit şifreleme sistemi          ║
║                                     ║
║  ↩️  30 Gün İade                    ║
║  İade şartları olmaksızın           ║
║                                     ║
║  ☎️  Müşteri Desteği               ║
║  7/24 profesyonel destek            ║
║                                     ║
║  📦 Ücretsiz Kargo                 ║
║  250 TL üzeri siparişlerde          ║
║                                     ║
╚═════════════════════════════════════╝
```

---

## 📋 ToolKit Sekmesi - Özellikler

```
┌─────────────────────────────────────┐
│ Özellikler Sekmesi                  │
├─────────────────────────────────────┤
│                                     │
│  ┌─────────────────────────────┐   │
│  │ 🌟 Premium Kalite           │   │
│  │    Yüksek kaliteli malz...  │   │
│  └─────────────────────────────┘   │
│                                     │
│  ┌─────────────────────────────┐   │
│  │ ⚡ Hızlı Teslim             │   │
│  │    24 saat içinde kargo...  │   │
│  └─────────────────────────────┘   │
│                                     │
│  ┌─────────────────────────────┐   │
│  │ 🔒 Güvenli Ödeme           │   │
│  │    256-bit şifreleme ile.. │   │
│  └─────────────────────────────┘   │
│                                     │
│  [Diğer sekmeler benzer kartlarla]  │
│                                     │
└─────────────────────────────────────┘
```

---

## 📊 ToolKit Sekmesi - Teknik Bilgi

```
┌─────────────────────────────────────┐
│ Teknik Bilgi Sekmesi                │
├─────────────────────────────────────┤
│                                     │
│  Marka          │  Mutlusan         │
│  ─────────────────────────────────  │
│                                     │
│  Ürün Kodu      │  KGM-1-31         │
│  ─────────────────────────────────  │
│                                     │
│  Stok Durumu    │  Stokta Var       │
│  ─────────────────────────────────  │
│                                     │
│  Stok Miktarı   │  14 adet          │
│  ─────────────────────────────────  │
│                                     │
│  Fiyat          │  ₺94,90           │
│  ─────────────────────────────────  │
│                                     │
│  ┌─────────────────────────────┐   │
│  │  Açıklama                   │   │
│  │  Günlük ev kullanımı için   │   │
│  │  güvenli uzatma prizi. Yoğ  │   │
│  │  kaliteli malzeme ile       │   │
│  │  yapılmıştır.              │   │
│  └─────────────────────────────┘   │
│                                     │
└─────────────────────────────────────┘
```

---

## 🚚 ToolKit Sekmesi - Kargo Seçenekleri

```
┌─────────────────────────────────────┐
│ Kargo Seçenekleri Sekmesi           │
├─────────────────────────────────────┤
│                                     │
│  ┌─────────────────────────────┐   │
│  │ 📦 Standart Kargo           │   │
│  │ 2-3 İş Günü      ₺29,99    │   │
│  └─────────────────────────────┘   │
│                                     │
│  ┌─────────────────────────────┐   │
│  │ ⚡ Hızlı Kargo   🌟POPÜLER  │   │
│  │ Ertesi Gün       ₺49,99    │   │
│  └─────────────────────────────┘   │
│                                     │
│  ┌─────────────────────────────┐   │
│  │ 🗺️  Sadece İstanbul          │   │
│  │ 3 Saat           ₺64,99    │   │
│  └─────────────────────────────┘   │
│                                     │
│  ℹ️  250 TL üzeri siparişlerde      │
│      kargo bedeli ödenmez.          │
│                                     │
└─────────────────────────────────────┘
```

---

## 🔄 ToolKit Sekmesi - İade Politikası

```
┌─────────────────────────────────────┐
│ İade Politikası Sekmesi             │
├─────────────────────────────────────┤
│                                     │
│  ┌──────────────────────────────┐  │
│  │ ✅ Koşulsuz İade  30 Gün    │  │
│  │    Hiçbir şart olmaksızın   │  │
│  │    iade edebilirsiniz       │  │
│  └──────────────────────────────┘  │
│                                     │
│  ┌──────────────────────────────┐  │
│  │ ✅ Ücretsiz Kargo  Dahil    │  │
│  │    İade kargo ücreti karşı  │  │
│  │    lanır                    │  │
│  └──────────────────────────────┘  │
│                                     │
│  ┌──────────────────────────────┐  │
│  │ ✅ Hızlı Para İadesi  5 Gün │  │
│  │    5 gün içinde para iadesi │  │
│  │    yapılır                  │  │
│  └──────────────────────────────┘  │
│                                     │
│  İade Süreci:                       │
│                                     │
│  ① Ürün kusurlu/farklı ise        │
│     başvuru yap                    │
│                                     │
│  ② Ücretsiz kargo etiketi al      │
│     ve gönder                      │
│                                     │
│  ③ Depo tarafından kontrol et    │
│                                     │
│  ④ Para iadesi işlemi başlat     │
│                                     │
└─────────────────────────────────────┘
```

---

## 🛒 Sepete Ekle Paneli (Bottom Bar)

```
┌─────────────────────────────────────┐
│                                     │
│  [Ürün Bilgileri →]                 │  <- Info Button
│                                     │
│  [−] 1 [+]    [✔ Sepete Ekle]      │  <- Main Controls
│                                     │
└─────────────────────────────────────┘
```

---

## 🎴 Ürün Karşılaştırma Kartı

```
┌─────────────────────────┐
│                         │
│   ┌─────────────────┐   │
│   │                 │   │  120pt
│   │   Ürün Görseli  │   │
│   │                 │   │
│   └─────────────────┘   │
│                         │
│  [Brand] MUTLUSAN      │
│                         │
│  Çiftli Priz Uzatma    │
│                         │
│  ₺94,90 [10%]          │
│                         │
│  🟢 Stokta Var         │
│                         │
│  [❤️ Favori] [🛒 Ekle] │
│                         │
└─────────────────────────┘
```

---

## 🎨 Renk Paletı

```
┌─────────────────────────┐
│  🟠 KGM Orange          │  #FF6B35 / RGB(255, 107, 53)
│     Birincil Renk       │
├─────────────────────────┤
│  🟢 Başarı Yeşil        │  #34C759 / System Green
│     Stok/Başarı         │
├─────────────────────────┤
│  ⚪ Sistem Gris         │  UIColor.systemGray
│     Devre Dışı         │
├─────────────────────────┤
│  ⬜ Sistem Arka Plan    │  UIColor.systemBackground
│     Dinamik             │
└─────────────────────────┘
```

---

## 📐 Tipografi

```
┌──────────────────────────────────┐
│  BAŞLIKLAR                        │
│  Bold, 22pt, Rounded Design       │
│  "Çiftli Priz Uzatma"             │
├──────────────────────────────────┤
│  ALT BAŞLIKLAR                    │
│  Semibold, 16pt                   │
│  "Kategoriler"                    │
├──────────────────────────────────┤
│  NORMAL METİN                     │
│  Regular, 14pt                    │
│  "Günlük ev kullanımı için..."   │
├──────────────────────────────────┤
│  İŞARETLER                        │
│  Bold, 12pt                       │
│  "[10% İNDİRİM]"                  │
└──────────────────────────────────┘
```

---

## 🔘 Buton Stilleri

```
┌─────────────────────────────┐
│  BIRINCIL BUTON (Orange)    │
│  ┌───────────────────────┐  │
│  │  Sepete Ekle         │  │
│  │  30pt Radius, White  │  │
│  └───────────────────────┘  │
├─────────────────────────────┤
│  SEKONDERİ BUTON (Gray)     │
│  ┌───────────────────────┐  │
│  │  Ürün Bilgileri →    │  │
│  │  10pt Radius         │  │
│  └───────────────────────┘  │
├─────────────────────────────┤
│  İKON BUTON                 │
│  ┌─────────────────────┐   │
│  │  ❤️  / 🛒  / 📌    │   │
│  │  Flexible Size      │   │
│  └─────────────────────┘   │
├─────────────────────────────┤
│  SEÇİM BUTONU (Picker)      │
│  [Özellikler|Teknik|Kargo] │
│  Segmented Style           │
└─────────────────────────────┘
```

---

## 📏 Boşluk ve Padding

```
┌─────────────────────────────┐
│   20pt - Dış Padding        │
│  ┌─────────────────────┐   │
│  │ 12pt - İç Padding   │   │
│  │ ┌─────────────────┐ │   │
│  │ │  Kart İçeriği   │ │   │
│  │ └─────────────────┘ │   │
│  │ 12pt - Araştırma    │   │
│  │ ┌─────────────────┐ │   │
│  │ │  Diğer Kart     │ │   │
│  │ └─────────────────┘ │   │
│  └─────────────────────┘   │
│   20pt - Dış Padding        │
└─────────────────────────────┘
```

---

## 🎬 Animasyonlar

| Animasyon | Kullanıldığı Yer | Stil |
|-----------|------------------|------|
| **Bottom Sheet** | ToolKit açılırken | easeInOut, 0.3s |
| **Fade** | Sekme geçişi | easeInOut, 0.2s |
| **Scale** | Buton seçimi | easeIn, 0.1s |
| **Parallax** | Resim scroll | continuous |
| **Opacity** | Icon durumu değişimi | easeIn, 0.2s |

---

## 🧩 Bileşen Hiyerarşisi

```
ProductDetailView
├─ ScrollView
│  └─ VStack
│     ├─ GeometryReader (Image Parallax)
│     │  └─ productImage()
│     └─ VStack (Content)
│        ├─ Brand Badge
│        ├─ Title & Price
│        ├─ Stock Status
│        ├─ Description
│        ├─ Categories
│        └─ Reviews
│
└─ safeAreaInset
   └─ stickyBottomBar()
      ├─ ToolKit Button
      ├─ Quantity Stepper
      └─ Add to Cart Button

ProductToolKit
├─ Picker (Segmented)
├─ ScrollView
└─ Switch-case
   ├─ featuresContent()
   ├─ specificationsContent()
   ├─ shippingContent()
   └─ returnsContent()
```

---

## 📱 Responsive Tasarım

### iPhone SE (375px)
- Padding: 16pt (normal 20pt)
- Font: -2pt (normal)
- Spacing: -4pt (normal)

### iPhone 14/15 (390-430px)
- Padding: 20pt (standart)
- Font: Normal
- Spacing: Normal

### iPhone 15 Plus (430px+)
- Padding: 24pt
- Font: +1pt
- Spacing: +4pt

---

## ♿ Erişilebilirlik

- `accessibilityLabel` tüm öğelerde
- `accessibilityHint` karmaşık butonlarda
- `VoiceOver` uyum kontrol edildi
- Renk zıtlığı test edildi
- Text size uyum dinamik

---

## 🔐 State Management

```swift
struct ProductDetailView {
    @State private var qty = 1              // Miktar
    @State private var addedToCart = false  // Sepete ekleme durumu
    @State private var showToolKit = false  // ToolKit gösterimi
    @StateObject private var viewModel      // Veri yönetimi
}

ProductToolKit {
    @State private var selectedTab          // Seçili sekme
}
```

---

**Son Güncelleme:** 2026-05-01  
**Uyum Sürümü:** iOS 14+  
**Durum:** Production Ready ✅
