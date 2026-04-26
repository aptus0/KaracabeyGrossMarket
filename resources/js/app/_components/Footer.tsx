import Link from "next/link";

type FooterProps = {
  compact?: boolean;
};

export function Footer({ compact = false }: FooterProps) {
  return (
    <footer className={compact ? "site-footer site-footer--compact" : "site-footer"}>
      <div className="site-footer__inner">
        <div>
          <strong>Karacabey Gross Market</strong>
          <p>Karacabey için hızlı market siparişi ve güvenli PayTR ödeme.</p>
        </div>
        <nav aria-label="Kurumsal">
          <Link href="/products">Ürünler</Link>
          <Link href="/kampanyalar">Kampanyalar</Link>
          <Link href="/kurumsal/hakkimizda">Hakkımızda</Link>
          <Link href="/kurumsal/kvkk">KVKK</Link>
          <Link href="/checkout">Checkout</Link>
          <Link href="/auth">Hesap</Link>
        </nav>
      </div>
    </footer>
  );
}
