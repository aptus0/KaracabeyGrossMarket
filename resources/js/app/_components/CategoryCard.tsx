import Link from "next/link";
import { Apple, type LucideIcon, Milk, Package, Wheat } from "lucide-react";
import type { KgmCategory } from "@/lib/catalog";

type CategoryCardProps = {
  category: KgmCategory;
};

export function CategoryCard({ category }: CategoryCardProps) {
  const Icon = categoryIcons[category.slug] ?? Package;

  return (
    <Link className="category-card" href={`/products?category=${category.slug}`}>
      <span className="category-card__icon">
        <Icon size={22} />
      </span>
      <span>{category.name}</span>
      <strong>{category.count} ürün</strong>
    </Link>
  );
}

const categoryIcons: Record<string, LucideIcon> = {
  "sut-ve-kahvaltilik": Milk,
  firin: Wheat,
  "meyve-sebze": Apple,
  "temel-gida": Package,
};
