"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useRef, useState } from "react";
import { Search, ShoppingCart, X } from "lucide-react";
import { KgmLogo } from "@/app/_components/KgmLogo";
import { cartItemCount } from "@/lib/cart";
import { useCartStore } from "@/lib/cart-store";

export function MobileHeader() {
  const cartCount = useCartStore((state) => cartItemCount(state.items));
  const openCartSheet = useCartStore((state) => state.openSheet);
  const [query, setQuery] = useState("");
  const inputRef = useRef<HTMLInputElement>(null);
  const router = useRouter();

  function handleSearch(e: React.FormEvent) {
    e.preventDefault();
    const q = query.trim();
    if (q) {
      router.push(`/products?q=${encodeURIComponent(q)}`);
    }
  }

  function handleClear() {
    setQuery("");
    inputRef.current?.focus();
  }

  return (
    <header className="mobile-header">
      {/* Top bar: Logo + Cart */}
      <div className="mobile-header__top">
        <Link href="/" className="brand-mark" aria-label="Karacabey Gross Market">
          <KgmLogo compact />
        </Link>

        <button
          type="button"
          className="mobile-header__cart"
          aria-label="Sepeti aç"
          onClick={openCartSheet}
        >
          <ShoppingCart size={20} />
          {cartCount > 0 ? (
            <span className="mobile-header__cart-badge">{cartCount}</span>
          ) : null}
        </button>
      </div>

      {/* Search bar */}
      <form className="mobile-header__search" onSubmit={handleSearch} role="search">
        <span className="mobile-header__search-icon" aria-hidden="true">
          <Search size={16} />
        </span>
        <input
          ref={inputRef}
          type="search"
          placeholder="Ürün, marka veya kategori ara…"
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          aria-label="Ürün ara"
        />
        {query.length > 0 ? (
          <button
            type="button"
            className="mobile-header__search-clear"
            aria-label="Aramayı temizle"
            onClick={handleClear}
          >
            <X size={14} />
          </button>
        ) : null}
        <button type="submit" className="mobile-header__search-btn">
          Ara
        </button>
      </form>
    </header>
  );
}
