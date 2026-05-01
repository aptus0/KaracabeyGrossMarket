"use client";

import Link from "next/link";
import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { useEffect, useRef, useState } from "react";
import {
  Bell,
  ChevronRight,
  LogOut,
  Menu,
  Search,
  ShoppingCart,
  User,
  X,
} from "lucide-react";
import { KgmLogo } from "@/app/_components/KgmLogo";
import { NavIcon } from "@/app/_components/NavIcon";
import { NotificationBell } from "@/app/_components/NotificationBell";
import { Button } from "@/app/_components/ui/button";
import {
  Sheet,
  SheetClose,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from "@/app/_components/ui/sheet";
import { useAuthStore } from "@/lib/auth-store";
import { cartItemCount, formatCartMoney } from "@/lib/cart";
import { useCartStore } from "@/lib/cart-store";
import {
  defaultCategoryMenu,
  fetchCategoryMenu,
  type CategoryMenuItem,
} from "@/lib/category-menu";
import { defaultNavigation, fetchNavigation, type NavigationData } from "@/lib/navigation";

export function MobileHeader() {
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const router = useRouter();
  const inputRef = useRef<HTMLInputElement>(null);
  const [query, setQuery] = useState(searchParams.get("q") ?? "");
  const [navigation, setNavigation] = useState<NavigationData>(defaultNavigation);
  const [categoryMenu, setCategoryMenu] = useState<CategoryMenuItem[]>(defaultCategoryMenu);

  const cartCount = useCartStore((state) => cartItemCount(state.items));
  const cartTotal = useCartStore((state) => state.total_cents);
  const openCartSheet = useCartStore((state) => state.openSheet);
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
  const logout = useAuthStore((state) => state.logout);

  useEffect(() => {
    const controller = new AbortController();
    fetchNavigation(controller.signal)
      .then(setNavigation)
      .catch(() => setNavigation(defaultNavigation));
    fetchCategoryMenu(controller.signal)
      .then(setCategoryMenu)
      .catch(() => setCategoryMenu(defaultCategoryMenu));
    return () => controller.abort();
  }, []);

  function handleSearch(event: React.FormEvent) {
    event.preventDefault();
    const trimmedQuery = query.trim();
    if (trimmedQuery) {
      router.push(`/products?q=${encodeURIComponent(trimmedQuery)}`);
      return;
    }
    router.push("/products");
  }

  return (
    <header className="mobile-header">
      {/* ── TOP BAR ────────────────────────────────────────────────────────── */}
      <div className="mobile-header__main">
        <div className="mobile-header__left">
          <Sheet>
            <SheetTrigger asChild>
              <button
                type="button"
                className="mobile-header__icon-btn"
                aria-label="Menüyü aç"
              >
                <Menu size={24} />
              </button>
            </SheetTrigger>
            <SheetContent side="left" className="flex flex-col border-none bg-white p-0 sm:max-w-[320px]">
              <SheetHeader className="border-b border-[#F1F5F9] px-6 py-5">
                <SheetTitle className="flex items-center gap-3">
                  <KgmLogo variant="app" compact />
                  <div className="grid gap-0.5 text-left">
                    <span className="text-sm font-black text-[#2B2F36]">Karacabey Market</span>
                    <span className="text-[10px] font-bold uppercase tracking-wider text-[#FF7A00]">Dijital Katalog</span>
                  </div>
                </SheetTitle>
              </SheetHeader>

              <div className="flex-1 overflow-y-auto px-4 py-6">
                <nav className="grid gap-1">
                  <div className="mb-2 px-2 text-[10px] font-black uppercase tracking-[0.2em] text-[#94A3B8]">Kategoriler</div>
                  {categoryMenu.map((item, idx) => (
                    <div key={item.slug} className="fade-in-item grid gap-2" style={{ animationDelay: `${idx * 0.05}s` }}>
                      <SheetClose asChild>
                        <Link
                          href={`/products?category=${encodeURIComponent(item.slug)}`}
                          className="flex items-center justify-between rounded-xl p-3 text-sm font-bold text-[#4B5E7A] transition hover:bg-[#FFF8F0] hover:text-[#FF7A00] active:scale-[0.98]"
                        >
                          <div className="flex items-center gap-3">
                            <span className="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#F8FAFC] text-[#FF7A00]">
                              <NavIcon name="grid" size={16} />
                            </span>
                            {item.name}
                          </div>
                          <ChevronRight size={14} className="opacity-40" />
                        </Link>
                      </SheetClose>

                      {item.children.length > 0 ? (
                        <div className="grid gap-1 pl-4">
                          {item.children.map((child, cIdx) => (
                            <SheetClose asChild key={child.slug}>
                              <Link
                                href={`/products?category=${encodeURIComponent(child.slug)}`}
                                className="rounded-xl px-3 py-2 text-xs font-bold text-[#6B7177] transition hover:bg-[#F8FAFC] hover:text-[#FF7A00]"
                                style={{ animationDelay: `${(idx * 0.05) + (cIdx * 0.02)}s` }}
                              >
                                {child.name}
                              </Link>
                            </SheetClose>
                          ))}
                        </div>
                      ) : null}
                    </div>
                  ))}

                  <div className="mb-2 mt-6 px-2 text-[10px] font-black uppercase tracking-[0.2em] text-[#94A3B8]">Hızlı Menü</div>
                  {defaultNavigation.header.map((item, idx) => (
                    <SheetClose asChild key={item.url}>
                      <Link
                        href={item.url}
                        className="fade-in-item flex items-center justify-between rounded-xl p-3 text-sm font-bold text-[#4B5E7A] transition hover:bg-[#FFF8F0] hover:text-[#FF7A00] active:scale-[0.98]"
                        style={{ animationDelay: `${(categoryMenu.length * 0.05) + (idx * 0.05)}s` }}
                      >
                        <div className="flex items-center gap-3">
                          <span className="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#F8FAFC] text-[#94A3B8]">
                            <NavIcon name={item.icon} size={16} />
                          </span>
                          {item.label}
                        </div>
                        <ChevronRight size={14} className="opacity-40" />
                      </Link>
                    </SheetClose>
                  ))}
                </nav>
              </div>

              <div className="border-t border-[#E4E7EB] p-6">
                {isAuthenticated ? (
                  <Button
                    variant="outline"
                    className="h-12 w-full justify-start gap-3 rounded-xl border-[#E4E7EB] text-[#4B5E7A]"
                    onClick={() => {
                      logout();
                      router.push("/");
                    }}
                  >
                    <LogOut size={18} />
                    Çıkış Yap
                  </Button>
                ) : (
                  <Button asChild className="h-12 w-full rounded-xl">
                    <Link href="/auth/login">Giriş Yap / Üye Ol</Link>
                  </Button>
                )}
              </div>
            </SheetContent>
          </Sheet>
        </div>

        <Link href="/" className="mobile-header__logo" aria-label="Karacabey Gross Market">
          <KgmLogo compact />
        </Link>

        <div className="mobile-header__right">
          <NotificationBell mobile />
          <button
            type="button"
            className="mobile-header__cart-btn"
            onClick={openCartSheet}
          >
            <ShoppingCart size={22} />
            {cartCount > 0 && (
              <span className="mobile-header__cart-badge">{cartCount}</span>
            )}
          </button>
        </div>
      </div>

      {/* ── SEARCH BAR ─────────────────────────────────────────────────────── */}
      <div className="px-4 pb-3 pt-1">
        <form className="mobile-header__search-compact" onSubmit={handleSearch}>
          <Search size={18} className="text-[#94A3B8]" />
          <input
            ref={inputRef}
            type="search"
            placeholder="Ürün veya marka ara..."
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            className="flex-1 bg-transparent text-sm font-bold text-[#2B2F36] outline-none placeholder:text-[#94A3B8]"
          />
          {query && (
            <button type="button" onClick={() => setQuery("")}>
              <X size={16} className="text-[#94A3B8]" />
            </button>
          )}
        </form>
      </div>
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
