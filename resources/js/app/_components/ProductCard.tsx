import Link from "next/link";
import type { KgmProduct } from "@/lib/catalog";
import { AddToCartButton } from "@/app/_components/AddToCartButton";
import { FavoriteButton } from "@/app/_components/FavoriteButton";
import { PriceBox } from "@/app/_components/PriceBox";

type ProductCardProps = {
  product: KgmProduct;
  priority?: boolean;
};

export function ProductCard({ product, priority = false }: ProductCardProps) {
  const imageUrl = safeImageUrl(product.image);

  return (
    <article className="product-card">
      <Link className="product-card__image" href={`/product/${product.slug}`}>
        {imageUrl ? (
          // eslint-disable-next-line @next/next/no-img-element
          <img
            src={imageUrl}
            alt={product.name}
            loading={priority ? "eager" : "lazy"}
          />
        ) : (
          <div className="flex h-full items-center justify-center text-sm font-black text-[#6B7177]">
            KGM
          </div>
        )}
      </Link>
      <div className="product-card__body">
        <div className="product-card__meta">
          <span>{product.badge}</span>
          <FavoriteButton productSlug={product.slug} />
        </div>
        <Link href={`/product/${product.slug}`}>
          <h3>{product.name}</h3>
        </Link>
        <p>{product.brand}</p>
        <PriceBox price={product.price} oldPrice={product.oldPrice} unit={product.unit} />
        <AddToCartButton productSlug={product.slug} className="w-full" />
      </div>
    </article>
  );
}

function safeImageUrl(url?: string | null) {
  if (!url) {
    return null;
  }

  try {
    const parsedUrl = new URL(url, "http://localhost");

    if (parsedUrl.protocol === "http:" || parsedUrl.protocol === "https:") {
      return url;
    }

    return null;
  } catch {
    return null;
  }
}
