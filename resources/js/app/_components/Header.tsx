"use client";

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
  X,
  User,
} from "lucide-react";

import { CheckoutSummary } from "@/app/_components/CheckoutSummary";
import { KgmLogo } from "@/app/_components/KgmLogo";
import { MegaMenu } from "@/app/_components/MegaMenu";
import { NavIcon } from "@/app/_components/NavIcon";
import { SearchBar } from "@/app/_components/SearchBar";
import type { KgmCartItem } from "@/lib/catalog";
import { useCartStore } from "@/lib/cart-store";
import {
  defaultNavigation,
  fetchNavigation,
  type NavigationData,
  type NavigationItem,
} from "@/lib/navigation";

type HeaderProps = {
  compact?: boolean;
};

export function Header({ compact = false }: HeaderProps) {
  const [navigation, setNavigation] =
    useState<NavigationData>(defaultNavigation);

  const [drawer, setDrawer] =
    useState<"menu" | "cart" | null>(null);

  const cartItems = useCartStore((state) => state.items);
  const cartCount = useCartStore((state) => state.count());

  useEffect(() => {
    const controller = new AbortController();

    fetchNavigation(controller.signal)
      .then(setNavigation)
      .catch(() => setNavigation(defaultNavigation));

    return () => controller.abort();
  }, []);

  useEffect(() => {
    document.body.classList.toggle(
      "drawer-open",
      drawer !== null
    );

    return () => {
      document.body.classList.remove("drawer-open");
    };
  }, [drawer]);

  useEffect(() => {
    if (drawer === null) {
      return;
    }

    function handleKeyDown(event: KeyboardEvent) {
      if (event.key === "Escape") {
        setDrawer(null);
      }
    }

    window.addEventListener("keydown", handleKeyDown);

    return () => {
      window.removeEventListener("keydown", handleKeyDown);
    };
  }, [drawer]);

  return (
    <header
      className={
        compact
          ? "site-header site-header--compact"
          : "site-header"
      }
    >
      <div className="site-header__announcement">
        <span>
          Haftalık gross fırsatları ve hızlı teslimat
          avantajları Karacabey Gross Market’te.
        </span>
      </div>

      <div className="site-header__main">
        <button
          type="button"
          className="header-action header-action--menu"
          aria-label="Menüyü aç"
          onClick={() => setDrawer("menu")}
        >
          <Menu size={21} />
        </button>

        <Link
          href="/"
          className="brand-mark"
          aria-label="Karacabey Gross Market"
        >
          <KgmLogo compact={compact} />
        </Link>

        {!compact && (
          <div className="site-header__search">
            <SearchBar />
          </div>
        )}

        <div
          className="header-actions"
          aria-label="Hızlı işlemler"
        >
          <IconLink
            href="/favorites"
            label="Favoriler"
            icon={<Heart size={20} />}
          />

          <IconLink
            href="/cargo-tracking"
            label="Kargo Takip"
            icon={<PackageSearch size={20} />}
          />

          <button
            type="button"
            className="header-action header-action--cart"
            aria-label="Sepet"
            onClick={() => setDrawer("cart")}
          >
            <ShoppingCart size={20} />
            <small>{cartCount}</small>
          </button>

          <IconLink
            href="/account"
            label="Hesabım"
            icon={<User size={20} />}
          />

          <IconLink
            href="/auth/login"
            label="Giriş"
            icon={<LogIn size={19} />}
            variant="login"
          />
        </div>
      </div>

      {!compact && (
        <div className="site-header__nav-row">
          <nav
            className="desktop-nav"
            aria-label="Ana menü"
          >
            <MegaMenu items={navigation.category} />

            {navigation.header.map((item) => (
              <HeaderNavLink
                key={`${item.label}-${item.url}`}
                item={item}
              />
            ))}
          </nav>
        </div>
      )}

      <HeaderDrawer
        drawer={drawer}
        navigation={navigation}
        cartItems={cartItems}
        onClose={() => setDrawer(null)}
      />
    </header>
  );
}

type IconLinkProps = {
  href: string;
  label: string;
  icon: ReactNode;
  variant?: "ghost" | "login";
};

function IconLink({
  href,
  label,
  icon,
  variant = "ghost",
}: IconLinkProps) {
  return (
    <Link
      href={href}
      className={`header-action header-action--${variant}`}
      aria-label={label}
    >
      {icon}
    </Link>
  );
}

type HeaderNavLinkProps = {
  item: NavigationItem;
  iconSize?: number;
};

function HeaderNavLink({
  item,
  iconSize = 16,
}: HeaderNavLinkProps) {
  return (
    <Link
      href={item.url}
      target={item.external ? "_blank" : undefined}
      rel={
        item.external
          ? "noopener noreferrer"
          : undefined
      }
    >
      <NavIcon
        name={item.icon}
        size={iconSize}
      />
      {item.label}
    </Link>
  );
}

type HeaderDrawerProps = {
  drawer: "menu" | "cart" | null;
  navigation: NavigationData;
  cartItems: KgmCartItem[];
  onClose: () => void;
};

function HeaderDrawer({
  drawer,
  navigation,
  cartItems,
  onClose,
}: HeaderDrawerProps) {
  const isOpen = drawer !== null;
  const isCartDrawer = drawer === "cart";

  return (
    <div
      className={`header-drawer header-drawer--${
        isCartDrawer ? "cart" : "menu"
      } ${isOpen ? "is-open" : ""}`}
      aria-hidden={!isOpen}
    >
      <button
        type="button"
        className="header-drawer__overlay"
        aria-label="Çekmeceyi kapat"
        onClick={onClose}
      />

      <aside
        className="header-drawer__panel"
        role="dialog"
        aria-modal="true"
        aria-label={isCartDrawer ? "Sepet" : "Menü"}
      >
        <div className="header-drawer__head">
          <strong>{isCartDrawer ? "Sepet" : "Menü"}</strong>
          <button
            type="button"
            aria-label="Kapat"
            onClick={onClose}
          >
            <X size={18} />
          </button>
        </div>

        {isCartDrawer ? (
          <div className="header-drawer__cart">
            <CheckoutSummary items={cartItems} />
            <Link
              href="/checkout"
              className="primary-action"
              onClick={onClose}
            >
              Ödemeye Geç
            </Link>
          </div>
        ) : (
          <div className="header-drawer__menu">
            <nav aria-label="Mobil menü">
              <Link href="/products" onClick={onClose}>
                <Grid3X3 size={18} />
                Tüm Ürünler
              </Link>

              {navigation.category.map((item) => (
                <HeaderDrawerLink
                  key={`category-${item.label}-${item.url}`}
                  item={item}
                  onClick={onClose}
                />
              ))}

              {navigation.header.map((item) => (
                <HeaderDrawerLink
                  key={`header-${item.label}-${item.url}`}
                  item={item}
                  onClick={onClose}
                />
              ))}

              {navigation.top.map((item) => (
                <HeaderDrawerLink
                  key={`top-${item.label}-${item.url}`}
                  item={item}
                  onClick={onClose}
                />
              ))}
            </nav>

            <div className="header-drawer__cards">
              <strong>Hızlı Erişim</strong>

              <Link href="/favorites" onClick={onClose}>
                <Heart size={18} />
                Favoriler
              </Link>

              <Link href="/account" onClick={onClose}>
                <User size={18} />
                Hesabım
              </Link>

              <Link href="/auth/login" onClick={onClose}>
                <LogIn size={18} />
                Giriş Yap
              </Link>
            </div>
          </div>
        )}
      </aside>
    </div>
  );
}

type HeaderDrawerLinkProps = {
  item: NavigationItem;
  onClick: () => void;
};

function HeaderDrawerLink({
  item,
  onClick,
}: HeaderDrawerLinkProps) {
  return (
    <Link
      href={item.url}
      target={item.external ? "_blank" : undefined}
      rel={
        item.external
          ? "noopener noreferrer"
          : undefined
      }
      onClick={onClick}
    >
      <NavIcon
        name={item.icon}
        size={18}
      />
      {item.label}
    </Link>
  );
}
