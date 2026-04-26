import Link from "next/link";

export function ShellHeader() {
  return (
    <header className="s1">
      <Link className="s2" href="/">
        Karacabey Gross Market
      </Link>
      <nav className="s3" aria-label="Ana menü">
        <Link href="/products">Ürünler</Link>
        <Link href="/checkout">Sepet</Link>
        <Link href="/account">Hesabım</Link>
      </nav>
      <Link className="s4" href="/auth/login">
        Giriş
      </Link>
    </header>
  );
}
