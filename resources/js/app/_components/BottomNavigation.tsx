import Link from "next/link";
import { Home, ShoppingCart, User, Grid3X3 } from "lucide-react";

export function BottomNavigation() {
  return (
    <nav className="bottom-nav" aria-label="Mobil menü">
      <Link href="/"><Home size={18} />Ana Sayfa</Link>
      <Link href="/products"><Grid3X3 size={18} />Ürünler</Link>
      <Link href="/checkout"><ShoppingCart size={18} />Sepet</Link>
      <Link href="/account"><User size={18} />Hesabım</Link>
    </nav>
  );
}
