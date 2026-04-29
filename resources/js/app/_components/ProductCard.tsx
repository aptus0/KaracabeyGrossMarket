import Link from "next/link";
import type { KgmProduct } from "@/lib/catalog";
import { AddToCartButton } from "@/app/_components/AddToCartButton";
import { FavoriteButton } from "@/app/_components/FavoriteButton";
import { formatPrice } from "@/lib/catalog";
import { cn } from "@/lib/utils";

type ProductCardProps = {
  product: KgmProduct;
  priority?: boolean;
};

export function ProductCard({ product, priority = false }: ProductCardProps) {
  const imageUrl = safeImageUrl(product.image);
  const isFallbackImage = imageUrl === "/assets/kgm-logo.png";
  const outOfStock = product.stock === 0;
  const hasDiscount = Boolean(product.oldPrice && product.oldPrice > product.price);

  return (
    <article className="product-card">
      {/* ── Image / media area ─────────────────────────────── */}
      <div className="product-card__media">
        <Link
          className={cn("product-card__img-link", isFallbackImage && "product-card__img-link--fallback")}
          href={`/product/${product.slug}`}
          tabIndex={-1}
        >
          {imageUrl ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img
              src={imageUrl}
              alt={product.name}
              loading={priority ? "eager" : "lazy"}
            />
          ) : (
            <div className="product-card__placeholder">KGM</div>
          )}
        </Link>

        {/* Badge */}
        {hasDiscount ? (
          <span className="product-card__badge product-card__badge--sale">İndirim</span>
        ) : outOfStock ? (
          <span className="product-card__badge product-card__badge--out">Tükendi</span>
        ) : product.badge ? (
          <span className="product-card__badge">{product.badge}</span>
        ) : null}

        {/* Favorites — appears on hover */}
        <div className="product-card__fav">
          <FavoriteButton productSlug={product.slug} />
        </div>

        {/* Hover overlay: actions */}
        <div className="product-card__overlay" aria-hidden="true">
          <AddToCartButton
            productSlug={product.slug}
            compact
            label="Sepete Ekle"
          />
          <Link className="product-card__view-btn" href={`/product/${product.slug}`} tabIndex={-1}>
            Ürüne Göz At
          </Link>
        </div>
      </div>

      {/* ── Body ───────────────────────────────────────────── */}
      <div className="product-card__body">
        <p className="product-card__brand">{product.brand}</p>
        <Link className="product-card__name-link" href={`/product/${product.slug}`}>
          <h3 className="product-card__name">{product.name}</h3>
        </Link>
        <div className="product-card__price">
          <strong>{formatPrice(product.price)}</strong>
          {product.oldPrice ? <s>{formatPrice(product.oldPrice)}</s> : null}
        </div>
      </div>
    </article>
  );
}

function safeImageUrl(url?: string | null) {
  if (!url) return null;
  try {
    const parsedUrl = new URL(url, "http://localhost");
    return parsedUrl.protocol === "http:" || parsedUrl.protocol === "https:" ? url : null;
  } catch {
    return null;
  }
}
