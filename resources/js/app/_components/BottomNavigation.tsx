"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import {
  Grid3X3,
  Heart,
  Home,
  ShoppingCart,
  User,
} from "lucide-react";
import { useCartStore } from "@/lib/cart-store";

const bottomNavItems = [
  {
    href: "/",
    label: "Ana Sayfa",
    icon: Home,
    match: (pathname: string) => pathname === "/",
  },
  {
    href: "/products",
    label: "Kategori",
    icon: Grid3X3,
    match: (pathname: string) =>
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
    href: "/favorites",
    label: "Favori",
    icon: Heart,
    match: (pathname: string) =>
      pathname.startsWith("/favorites"),
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
  const cartCount = useCartStore((state) => state.count());

  return (
    <nav className="bottom-nav" aria-label="Mobil app bar">
      {bottomNavItems.map((item) => {
        const Icon = item.icon;
        const isActive = item.match(pathname);

        return (
          <Link
            key={item.href}
            href={item.href}
            className={`bottom-nav__item${
              isActive ? " is-active" : ""
            }${
              item.variant === "primary"
                ? " bottom-nav__item--primary"
                : ""
            }`}
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
