import Link from "next/link";
import type { KgmCategory } from "@/lib/catalog";

type CategoryCardProps = {
  category: KgmCategory;
};

export function CategoryCard({ category }: CategoryCardProps) {
  return (
    <Link className="category-card" href={`/products?category=${category.slug}`}>
      <span>{category.name}</span>
      <strong>{category.count} ürün</strong>
    </Link>
  );
}
