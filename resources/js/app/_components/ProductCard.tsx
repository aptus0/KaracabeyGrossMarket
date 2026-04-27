import Image from "next/image";
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
  return (
    <article className="product-card">
      <Link className="product-card__image" href={`/product/${product.slug}`}>
        <Image
          src={product.image}
          alt={product.name}
          fill
          priority={priority}
          sizes="(max-width: 620px) 50vw, (max-width: 980px) 33vw, 25vw"
        />
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
