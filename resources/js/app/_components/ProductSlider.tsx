import type { KgmProduct } from "@/lib/catalog";
import { ProductCard } from "@/app/_components/ProductCard";

type ProductSliderProps = {
  products: KgmProduct[];
};

export function ProductSlider({ products }: ProductSliderProps) {
  return (
    <div className="product-slider" aria-label="Öne çıkan ürünler">
      {products.map((product) => (
        <div className="product-slider__item" key={product.slug}>
          <ProductCard product={product} />
        </div>
      ))}
    </div>
  );
}
