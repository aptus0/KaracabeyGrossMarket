import Link from "next/link";

export function BottomNavigation() {
  return (
    <nav className="bottom-nav" aria-label="Mobil menü">
      <Link href="/">Ana Sayfa</Link>
      <Link href="/products">Ürünler</Link>
      <Link href="/checkout">Sepet</Link>
      <Link href="/account">Hesabım</Link>
    </nav>
  );
}
