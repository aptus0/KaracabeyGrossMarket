"use client";

import dynamic from "next/dynamic";
import Link from "next/link";
import type { ReactNode } from "react";
import { useEffect, useState } from "react";
import {
  Grid3X3,
  Heart,
  LogIn,
  Menu,
  PackageSearch,
  ShoppingCart,
  User,
  X,
} from "lucide-react";
import { KgmLogo } from "@/app/_components/KgmLogo";
import { MegaMenu } from "@/app/_components/MegaMenu";
import { NavIcon } from "@/app/_components/NavIcon";
import { SearchBar } from "@/app/_components/SearchBar";
import { useAuthStore } from "@/lib/auth-store";
import { cartItemCount } from "@/lib/cart";
import { useCartStore } from "@/lib/cart-store";
import {
  defaultNavigation,
  fetchNavigation,
  type NavigationData,
  type NavigationItem,
} from "@/lib/navigation";

const CartSheet = dynamic(
  () => import("@/app/_components/CartSheet").then((module) => module.CartSheet),
  { ssr: false },
);

type HeaderProps = {
  compact?: boolean;
};

export function Header({ compact = false }: HeaderProps) {
  const [navigation, setNavigation] = useState<NavigationData>(defaultNavigation);
  const [menuOpen, setMenuOpen] = useState(false);
  const cartCount = useCartStore((state) => cartItemCount(state.items));
  const isCartOpen = useCartStore((state) => state.isSheetOpen);
  const openCartSheet = useCartStore((state) => state.openSheet);
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
  const user = useAuthStore((state) => state.user);
  const firstName = (user?.name ?? "Müşteri").split(" ")[0];

  useEffect(() => {
    const controller = new AbortController();

    fetchNavigation(controller.signal)
      .then(setNavigation)
      .catch(() => setNavigation(defaultNavigation));

    return () => controller.abort();
  }, []);

  useEffect(() => {
    document.body.classList.toggle("drawer-open", menuOpen);

    return () => {
      document.body.classList.remove("drawer-open");
    };
  }, [menuOpen]);

  useEffect(() => {
    if (!menuOpen) {
      return;
    }

    function handleKeyDown(event: KeyboardEvent) {
      if (event.key === "Escape") {
        setMenuOpen(false);
      }
    }

    window.addEventListener("keydown", handleKeyDown);

    return () => {
      window.removeEventListener("keydown", handleKeyDown);
    };
  }, [menuOpen]);

  return (
    <header className={compact ? "site-header site-header--compact" : "site-header"}>
      <div className="site-header__announcement">
        <div className="site-header__announcement-inner">
          <span className="site-header__announcement-copy">
            Karacabey Gross Market için güvenli ödeme, canlı stok ve düzenli teslimat operasyonu.
          </span>

          {!compact ? (
            <div className="site-header__top-links" aria-label="Hızlı bağlantılar">
              {navigation.top.map((item) => (
                <TopUtilityLink key={`topbar-${item.label}-${item.url}`} item={item} />
              ))}
            </div>
          ) : null}
        </div>
      </div>

      <div className="site-header__main">
        <div className="site-header__brand-cluster">
          <button
            type="button"
            className="header-action header-action--menu"
            aria-label="Menüyü aç"
            onClick={() => setMenuOpen(true)}
          >
            <Menu size={21} />
          </button>

          <Link href="/" className="brand-mark" aria-label="Karacabey Gross Market">
            <KgmLogo compact={compact} />
          </Link>
        </div>

        {!compact ? (
          <div className="site-header__search">
            <SearchBar />
          </div>
        ) : null}

        <div className="header-actions" aria-label="Hızlı işlemler">
          <IconLink href="/favorites" label="Favoriler" icon={<Heart size={20} />} mobileHidden />
          <IconLink href="/cargo-tracking" label="Kargo Takip" icon={<PackageSearch size={20} />} mobileHidden />

          <button
            type="button"
            className="header-action header-action--cart"
            aria-label="Sepet"
            aria-expanded={isCartOpen}
            aria-haspopup="dialog"
            onClick={openCartSheet}
          >
            <ShoppingCart size={20} />
            <small>{cartCount}</small>
          </button>

          <HeaderAccountLink isAuthenticated={isAuthenticated} userName={firstName} />
        </div>
      </div>

      {!compact ? (
        <div className="site-header__nav-row">
          <div className="site-header__nav-shell">
            <div className="site-header__nav-primary">
              <MegaMenu items={navigation.category} />

              <nav className="desktop-nav" aria-label="Ana menü">
                {navigation.header.map((item) => (
                  <HeaderNavLink key={`${item.label}-${item.url}`} item={item} />
                ))}
              </nav>
            </div>

            <Link href="/kurumsal/iletisim" className="site-header__support-link">
              Destek ve iletişim
            </Link>
          </div>
        </div>
      ) : null}

      <HeaderDrawer
        isOpen={menuOpen}
        navigation={navigation}
        isAuthenticated={isAuthenticated}
        userName={user?.name ?? null}
        onClose={() => setMenuOpen(false)}
      />

      <CartSheet />
    </header>
  );
}

function TopUtilityLink({ item }: { item: NavigationItem }) {
  return (
    <Link
      href={item.url}
      target={item.external ? "_blank" : undefined}
      rel={item.external ? "noopener noreferrer" : undefined}
      className="header-utility-link"
    >
      <NavIcon name={item.icon} size={14} />
      <span>{item.label}</span>
    </Link>
  );
}

type IconLinkProps = {
  href: string;
  label: string;
  icon: ReactNode;
  variant?: "ghost" | "login";
  mobileHidden?: boolean;
};

function IconLink({
  href,
  label,
  icon,
  variant = "ghost",
  mobileHidden = false,
}: IconLinkProps) {
  return (
    <Link
      href={href}
      className={`header-action header-action--${variant}${mobileHidden ? " header-action--desktop-only" : ""}`}
      aria-label={label}
    >
      {icon}
    </Link>
  );
}

function HeaderAccountLink({
  isAuthenticated,
  userName,
}: {
  isAuthenticated: boolean;
  userName: string;
}) {
  return (
    <Link
      href={isAuthenticated ? "/account" : "/auth/login"}
      className="header-account-button header-action--desktop-only"
      aria-label={isAuthenticated ? "Hesabım" : "Giriş Yap"}
    >
      {isAuthenticated ? <User size={18} /> : <LogIn size={18} />}
      <span>{isAuthenticated ? `Merhaba ${userName}` : "Giriş Yap"}</span>
    </Link>
  );
}

type HeaderNavLinkProps = {
  item: NavigationItem;
  iconSize?: number;
};

function HeaderNavLink({ item, iconSize = 16 }: HeaderNavLinkProps) {
  return (
    <Link
      href={item.url}
      target={item.external ? "_blank" : undefined}
      rel={item.external ? "noopener noreferrer" : undefined}
    >
      <NavIcon name={item.icon} size={iconSize} />
      {item.label}
    </Link>
  );
}

type HeaderDrawerProps = {
  isOpen: boolean;
  navigation: NavigationData;
  isAuthenticated: boolean;
  userName: string | null;
  onClose: () => void;
};

function HeaderDrawer({
  isOpen,
  navigation,
  isAuthenticated,
  userName,
  onClose,
}: HeaderDrawerProps) {
  return (
    <div className={`header-drawer header-drawer--menu ${isOpen ? "is-open" : ""}`} aria-hidden={!isOpen}>
      <button
        type="button"
        className="header-drawer__overlay"
        aria-label="Çekmeceyi kapat"
        onClick={onClose}
      />

      <aside className="header-drawer__panel" role="dialog" aria-modal="true" aria-label="Menü">
        <div className="header-drawer__head">
          <div className="grid gap-1">
            <strong>Menü</strong>
            {isAuthenticated ? (
              <span className="text-sm font-semibold text-[#6B7177]">Merhaba {userName?.split(" ")[0] ?? "müşteri"}</span>
            ) : null}
          </div>
          <button type="button" aria-label="Kapat" onClick={onClose}>
            <X size={18} />
          </button>
        </div>

        <div className="header-drawer__menu">
          <nav aria-label="Mobil menü">
            <Link href="/products" onClick={onClose}>
              <Grid3X3 size={18} />
              Tüm Ürünler
            </Link>

            {navigation.category.map((item) => (
              <HeaderDrawerLink key={`category-${item.label}-${item.url}`} item={item} onClick={onClose} />
            ))}

            {navigation.header.map((item) => (
              <HeaderDrawerLink key={`header-${item.label}-${item.url}`} item={item} onClick={onClose} />
            ))}

            {navigation.top.map((item) => (
              <HeaderDrawerLink key={`top-${item.label}-${item.url}`} item={item} onClick={onClose} />
            ))}
          </nav>

          <div className="header-drawer__cards">
            <strong>Hızlı Erişim</strong>

            <Link href="/checkout" onClick={onClose}>
              <ShoppingCart size={18} />
              Sepetim
            </Link>

            <Link href="/cargo-tracking" onClick={onClose}>
              <PackageSearch size={18} />
              Kargo Takip
            </Link>

            <Link href="/favorites" onClick={onClose}>
              <Heart size={18} />
              Favoriler
            </Link>

            <Link href="/account" onClick={onClose}>
              <User size={18} />
              Hesabım
            </Link>

            {!isAuthenticated ? (
              <Link href="/auth/login" onClick={onClose}>
                <LogIn size={18} />
                Giriş Yap
              </Link>
            ) : null}
          </div>
        </div>
      </aside>
    </div>
  );
}

type HeaderDrawerLinkProps = {
  item: NavigationItem;
  onClick: () => void;
};

function HeaderDrawerLink({ item, onClick }: HeaderDrawerLinkProps) {
  return (
    <Link
      href={item.url}
      target={item.external ? "_blank" : undefined}
      rel={item.external ? "noopener noreferrer" : undefined}
      onClick={onClick}
    >
      <NavIcon name={item.icon} size={18} />
      {item.label}
    </Link>
  );
}
