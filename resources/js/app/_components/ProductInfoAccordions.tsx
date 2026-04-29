import Link from "next/link";
import { ClipboardList, MessageSquareText, ShieldCheck, Sparkles, Star } from "lucide-react";
import type { KgmProduct } from "@/lib/catalog";

type ProductInfoAccordionsProps = {
  product: KgmProduct;
};

export function ProductInfoAccordions({ product }: ProductInfoAccordionsProps) {
  const stockLabel = product.stock > 0 ? `${product.stock} adet` : "Stok teyidi gerekli";
  const features = [
    ["Marka", product.brand],
    ["Kategori", product.categoryName ?? "Genel katalog"],
    ["Ürün kodu", product.sku ?? product.slug],
    ["Birim", product.unit],
    ["Stok durumu", stockLabel],
  ];

  return (
    <div className="product-info-accordion" aria-label="Ürün bilgileri">
      <details className="product-info-accordion__item" open>
        <summary>
          <span>
            <ClipboardList size={18} />
            Açıklama
          </span>
        </summary>
        <div className="product-info-accordion__content">
          <p>
            {product.description}
          </p>
          <p>
            {product.name} Karacabey Gross Market kataloğunda güncel fiyat ve hızlı sipariş akışıyla listelenir.
          </p>
        </div>
      </details>

      <details className="product-info-accordion__item">
        <summary>
          <span>
            <Sparkles size={18} />
            Özellikler
          </span>
        </summary>
        <div className="product-info-accordion__content">
          <dl className="product-spec-list">
            {features.map(([label, value]) => (
              <div key={label}>
                <dt>{label}</dt>
                <dd>{value}</dd>
              </div>
            ))}
          </dl>
        </div>
      </details>

      <details className="product-info-accordion__item">
        <summary>
          <span>
            <ShieldCheck size={18} />
            Teslimat ve Güvence
          </span>
        </summary>
        <div className="product-info-accordion__content">
          <ul className="product-info-list">
            <li>Karacabey içi teslimat akışı sipariş sırasında adres bilgisine göre planlanır.</li>
            <li>Ürün fiyatı ve stok durumu canlı katalog üzerinden kontrol edilir.</li>
            <li>Sipariş sonrası sepet ve ödeme adımlarında ürün özeti tekrar gösterilir.</li>
          </ul>
        </div>
      </details>

      <details className="product-info-accordion__item">
        <summary>
          <span>
            <MessageSquareText size={18} />
            Değerlendirmeler
          </span>
        </summary>
        <div className="product-info-accordion__content product-review-empty">
          <div className="product-review-empty__stars" aria-hidden="true">
            {Array.from({ length: 5 }).map((_, index) => (
              <Star key={index} size={17} />
            ))}
          </div>
          <p>Bu ürün için henüz değerlendirme bulunmuyor.</p>
          <Link href="/auth/login" className="product-info-accordion__button">
            Değerlendirme yaz
          </Link>
        </div>
      </details>
    </div>
  );
}
