"use client";

import Link from "next/link";
import { useId, useState } from "react";
import { ChevronDown } from "lucide-react";

import { NavIcon } from "@/app/_components/NavIcon";
import type { NavigationItem } from "@/lib/navigation";

type MegaMenuProps = {
  items: NavigationItem[];
};

export function MegaMenu({ items }: MegaMenuProps) {
  const [isOpen, setIsOpen] = useState(false);
  const panelId = useId();

  return (
    <div
      className={`mega-menu${isOpen ? " is-open" : ""}`}
      onMouseEnter={() => setIsOpen(true)}
      onMouseLeave={() => setIsOpen(false)}
    >
      <button
        className="mega-menu__trigger"
        type="button"
        aria-haspopup="menu"
        aria-expanded={isOpen}
        aria-controls={panelId}
        onClick={() => setIsOpen((open) => !open)}
      >
        Kategoriler
        <ChevronDown size={15} />
      </button>

      <div
        id={panelId}
        className="mega-menu__panel"
        role="menu"
        aria-label="Kategoriler"
      >
        <div className="mega-menu__heading">
          <strong>Popüler Kategoriler</strong>
          <span>Karacabey Gross Market reyonları</span>
        </div>

        <div className="mega-menu__grid">
          {items.map((item) => (
            <Link
              key={`${item.label}-${item.url}`}
              href={item.url}
              target={item.external ? "_blank" : undefined}
              rel={
                item.external
                  ? "noopener noreferrer"
                  : undefined
              }
              role="menuitem"
              onClick={() => setIsOpen(false)}
            >
              <NavIcon name={item.icon} />
              <span>{item.label}</span>
            </Link>
          ))}
        </div>
      </div>
    </div>
  );
}
