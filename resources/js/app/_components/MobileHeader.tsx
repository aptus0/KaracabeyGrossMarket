"use client";

import Link from "next/link";
import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { useRef, useState } from "react";
import { Grid3X3, Home, Search, ShoppingCart, Tag, User, X } from "lucide-react";
import { KgmLogo } from "@/app/_components/KgmLogo";
import { useAuthStore } from "@/lib/auth-store";
import { cartItemCount, formatCartMoney } from "@/lib/cart";
import { useCartStore } from "@/lib/cart-store";

export function MobileHeader() {
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const router = useRouter();
  const inputRef = useRef<HTMLInputElement>(null);
  const [query, setQuery] = useState(searchParams.get("q") ?? "");

  const cartCount = useCartStore((state) => cartItemCount(state.items));
  const cartTotal = useCartStore((state) => state.total_cents);
  const openCartSheet = useCartStore((state) => state.openSheet);
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
  const pageContext = resolvePageContext(pathname, searchParams.get("category"), searchParams.get("q"));
  const quickLinks = [
    { href: "/", label: "Ana Sayfa", icon: Home, match: (value: string) => value === "/" },
    { href: "/products", label: "Urunler", icon: Grid3X3, match: (value: string) => value.startsWith("/products") || value.startsWith("/product") },
    { href: "/kampanyalar", label: "Firsatlar", icon: Tag, match: (value: string) => value.startsWith("/kampanyalar") },
    {
      href: isAuthenticated ? "/account" : "/auth/login",
      label: isAuthenticated ? "Hesabim" : "Giris",
      icon: User,
      match: (value: string) => value.startsWith("/account") || value.startsWith("/addresses") || value.startsWith("/auth"),
    },
  ];

  function handleSearch(event: React.FormEvent) {
    event.preventDefault();
    const trimmedQuery = query.trim();

    if (trimmedQuery) {
      router.push(`/products?q=${encodeURIComponent(trimmedQuery)}`);
      return;
    }

    router.push("/products");
  }

  function handleClear() {
    setQuery("");
    inputRef.current?.focus();
  }

  return (
    <header className="mobile-header">
      <div className="mobile-header__meta">
        <span className="mobile-header__eyebrow">{pageContext.eyebrow}</span>
        <span className="mobile-header__meta-pill">{pageContext.detail}</span>
      </div>

      <div className="mobile-header__top">
        <Link href="/" className="brand-mark mobile-header__brand" aria-label="Karacabey Gross Market">
          <KgmLogo compact />
        </Link>

        <div className="mobile-header__actions">
          <Link
            href={isAuthenticated ? "/account" : "/auth/login"}
            className="mobile-header__account-pill"
            aria-label={isAuthenticated ? "Hesabim" : "Giris yap"}
          >
            <User size={15} />
            <span>{isAuthenticated ? "Hesabim" : "Giris"}</span>
          </Link>

          <button
            type="button"
            className="mobile-header__cart"
            aria-label="Sepeti ac"
            onClick={openCartSheet}
          >
            <span className="mobile-header__cart-icon">
              <ShoppingCart size={18} />
            </span>
            <span className="mobile-header__cart-copy">
              <strong>{cartCount > 0 ? `${cartCount} urun` : "Sepet"}</strong>
              <small>{cartCount > 0 ? formatCartMoney(cartTotal) : "Canli ozet"}</small>
            </span>
            {cartCount > 0 ? (
              <span className="mobile-header__cart-badge">{cartCount}</span>
            ) : null}
          </button>
        </div>
      </div>

      <form className="mobile-header__search" onSubmit={handleSearch} role="search">
        <span className="mobile-header__search-icon" aria-hidden="true">
          <Search size={16} />
        </span>
        <input
          ref={inputRef}
          type="search"
          placeholder="Urun, marka veya kategori ara"
          value={query}
          onChange={(event) => setQuery(event.target.value)}
          aria-label="Urun ara"
        />
        {query.length > 0 ? (
          <button
            type="button"
            className="mobile-header__search-clear"
            aria-label="Aramayi temizle"
            onClick={handleClear}
          >
            <X size={14} />
          </button>
        ) : null}
        <button type="submit" className="mobile-header__search-btn">
          Ara
        </button>
      </form>

      <nav className="mobile-header__shortcuts" aria-label="Mobil hizli erisim">
        {quickLinks.map((item) => {
          const Icon = item.icon;
          const isActive = item.match(pathname);

          return (
            <Link
              key={item.href}
              href={item.href}
              className={`mobile-header__shortcut${isActive ? " mobile-header__shortcut--active" : ""}`}
              aria-current={isActive ? "page" : undefined}
            >
              <Icon size={15} />
              <span>{item.label}</span>
            </Link>
          );
        })}
      </nav>
    </header>
  );
}

function resolvePageContext(pathname: string, category: string | null, query: string | null) {
  if (pathname === "/") {
    return {
      eyebrow: "Mobil Market",
      detail: "Bugun alinacaklar hazir",
    };
  }

  if (pathname.startsWith("/products")) {
    return {
      eyebrow: "Katalog",
      detail: query?.trim()
        ? `"${query.trim()}" aramasi`
        : category?.trim()
          ? `${category.trim()} kategorisi`
          : "Tum urunler",
    };
  }

  if (pathname.startsWith("/product")) {
    return {
      eyebrow: "Urun Detay",
      detail: "Hizli sepete ekleme",
    };
  }

  if (pathname.startsWith("/checkout")) {
    return {
      eyebrow: "Guvenli Odeme",
      detail: "Canli sepet ozeti",
    };
  }

  if (pathname.startsWith("/kampanyalar")) {
    return {
      eyebrow: "Kampanyalar",
      detail: "Guncel firsatlar",
    };
  }

  return {
    eyebrow: "Karacabey Gross",
    detail: "Hizli teslimat akisi",
  };
}
