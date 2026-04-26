import Link from "next/link";
import { categories } from "@/lib/catalog";

export function MegaMenu() {
  return (
    <nav className="mega-menu" aria-label="Kategoriler">
      {categories.map((category) => (
        <Link key={category.slug} href={`/products?category=${category.slug}`}>
          {category.name}
        </Link>
      ))}
      <Link href="/products">Tüm Ürünler</Link>
    </nav>
  );
}
