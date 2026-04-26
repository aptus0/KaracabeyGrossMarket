import Link from "next/link";
import {
  Heart,
  MapPin,
  PackageSearch,
  ShoppingCart,
  User,
  LogIn,
  Home,
} from "lucide-react";

import { MegaMenu } from "@/app/_components/MegaMenu";
import { MobileHeader } from "@/app/_components/MobileHeader";
import { SearchBar } from "@/app/_components/SearchBar";

type HeaderProps = {
  compact?: boolean;
};

export function Header({ compact = false }: HeaderProps) {
  return (
    <header className={compact ? "site-header site-header--compact" : "site-header"}>
      <div className="site-header__top">
        <div className="site-header__top-inner">
          <span>Karacabey Gross Market’e hoş geldiniz</span>

          <div className="site-header__top-links">
            <Link href="/cargo-tracking">
              <PackageSearch size={15} />
              Kargo Takip
            </Link>

            <Link href="/addresses">
              <MapPin size={15} />
              Adresim
            </Link>
          </div>
        </div>
      </div>

      <div className="site-header__inner">
        <Link className="brand-mark" href="/">
          <span className="brand-mark__icon">
            <Home size={24} />
          </span>

          <span className="brand-mark__text">
            <span>Karacabey</span>
            <strong>Gross Market</strong>
          </span>
        </Link>

        {compact ? null : (
          <div className="site-header__search">
            <SearchBar />
          </div>
        )}

        <nav className="desktop-nav" aria-label="Ana menü">
          <Link href="/products">Ürünler</Link>
          <Link href="/categories">Kategoriler</Link>
          <Link href="/corporate/about">Hakkımızda</Link>
        </nav>

        <div className="header-actions">
          <Link className="header-action header-action--ghost" href="/favorites">
            <Heart size={19} />
            <span>Favoriler</span>
          </Link>

          <Link className="header-action header-action--cart" href="/checkout">
            <ShoppingCart size={19} />
            <span>Sepet</span>
            <small>0</small>
          </Link>

          <Link className="header-action header-action--ghost" href="/account">
            <User size={19} />
            <span>Hesabım</span>
          </Link>

          <Link className="header-action header-action--login" href="/auth/login">
            <LogIn size={18} />
            <span>Giriş</span>
          </Link>
        </div>
      </div>

      {compact ? null : <MegaMenu />}
      <MobileHeader />
    </header>
  );
}