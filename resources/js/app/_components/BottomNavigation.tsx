"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import {
  Grid3X3,
  ShoppingCart,
  User,
} from "lucide-react";
import { cartItemCount } from "@/lib/cart";
import { useCartStore } from "@/lib/cart-store";

const bottomNavItems = [
  {
    href: "/products",
    label: "Ürünler",
    icon: Grid3X3,
    match: (pathname: string) =>
      pathname === "/" ||
      pathname.startsWith("/products") ||
      pathname.startsWith("/product"),
  },
  {
    href: "/checkout",
    label: "Sepet",
    icon: ShoppingCart,
    variant: "primary" as const,
    match: (pathname: string) =>
      pathname.startsWith("/checkout"),
  },
  {
    href: "/account",
    label: "Hesabım",
    icon: User,
    match: (pathname: string) =>
      pathname.startsWith("/account") ||
      pathname.startsWith("/addresses") ||
      pathname.startsWith("/auth"),
  },
];

export function BottomNavigation() {
  const pathname = usePathname();
  const cartCount = useCartStore((state) => cartItemCount(state.items));
  const isCartOpen = useCartStore((state) => state.isSheetOpen);
  const openCartSheet = useCartStore((state) => state.openSheet);

  return (
    <nav className="bottom-nav" aria-label="Mobil app bar">
      {bottomNavItems.map((item) => {
        const Icon = item.icon;
        const isActive = item.href === "/checkout" ? pathname.startsWith("/checkout") || isCartOpen : item.match(pathname);
        const className = `bottom-nav__item${
          isActive ? " is-active" : ""
        }${
          item.variant === "primary"
            ? " bottom-nav__item--primary"
            : ""
        }`;

        if (item.href === "/checkout") {
          return (
            <button
              key={item.href}
              type="button"
              className={className}
              aria-label="Sepeti aç"
              aria-expanded={isCartOpen}
              aria-haspopup="dialog"
              onClick={openCartSheet}
            >
              <Icon size={18} />
              <span>{item.label}</span>
              {cartCount > 0 ? (
                <small className="bottom-nav__badge">{cartCount}</small>
              ) : null}
            </button>
          );
        }

        return (
          <Link
            key={item.href}
            href={item.href}
            className={className}
            aria-current={isActive ? "page" : undefined}
          >
            <Icon size={18} />
            <span>{item.label}</span>
            {item.href === "/checkout" && cartCount > 0 ? (
              <small className="bottom-nav__badge">{cartCount}</small>
            ) : null}
          </Link>
        );
      })}
    </nav>
  );
}
