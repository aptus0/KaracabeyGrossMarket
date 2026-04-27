import Link from "next/link";
import { ShoppingCart } from "lucide-react";
import { KgmLogo } from "@/app/_components/KgmLogo";
import { NavIcon } from "@/app/_components/NavIcon";
import { SearchBar } from "@/app/_components/SearchBar";
import type { NavigationData } from "@/lib/navigation";

type MobileHeaderProps = {
  navigation: NavigationData;
};

export function MobileHeader({ navigation }: MobileHeaderProps) {
  return (
    <div className="mobile-header">
      <div className="mobile-header__top">
        <Link className="brand-mark" href="/">
          <KgmLogo compact />
        </Link>
        <Link className="header-action" href="/checkout">
          <ShoppingCart size={18} />
          Sepet
        </Link>
      </div>
      <SearchBar compact />
      <nav className="mobile-header__links" aria-label="Mobil header menusu">
        {navigation.category.slice(0, 1).map((item) => (
          <Link key={`category-${item.label}-${item.url}`} href={item.url} target={item.external ? "_blank" : undefined}>
            <NavIcon name={item.icon} />
            Kategoriler
          </Link>
        ))}
        {navigation.header.slice(0, 4).map((item) => (
          <Link key={`${item.label}-${item.url}`} href={item.url} target={item.external ? "_blank" : undefined}>
            <NavIcon name={item.icon} />
            {item.label}
          </Link>
        ))}
      </nav>
    </div>
  );
}
