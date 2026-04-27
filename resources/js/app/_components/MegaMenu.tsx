import Link from "next/link";
import { ChevronDown } from "lucide-react";
import { NavIcon } from "@/app/_components/NavIcon";
import type { NavigationItem } from "@/lib/navigation";

type MegaMenuProps = {
  items: NavigationItem[];
};

export function MegaMenu({ items }: MegaMenuProps) {
  return (
    <div className="mega-menu">
      <button className="mega-menu__trigger" type="button" aria-haspopup="true">
        Kategoriler
        <ChevronDown size={15} />
      </button>
      <div className="mega-menu__panel" role="menu" aria-label="Kategoriler">
        <div className="mega-menu__heading">
          <strong>Popüler Kategoriler</strong>
          <span>Karacabey Gross Market reyonları</span>
        </div>
        <div className="mega-menu__grid">
          {items.map((item) => (
            <Link key={`${item.label}-${item.url}`} href={item.url} target={item.external ? "_blank" : undefined} role="menuitem">
              <NavIcon name={item.icon} />
              <span>{item.label}</span>
            </Link>
          ))}
        </div>
      </div>
    </div>
  );
}
