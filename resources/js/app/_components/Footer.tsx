"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { ShieldCheck, Tag, Truck } from "lucide-react";
import { KgmLogo } from "@/app/_components/KgmLogo";
import { NavIcon } from "@/app/_components/NavIcon";
import { defaultNavigation, fetchNavigation, type NavigationData, type NavigationItem } from "@/lib/navigation";

type FooterProps = {
  compact?: boolean;
};

export function Footer({ compact = false }: FooterProps) {
  const [navigation, setNavigation] = useState<NavigationData>(defaultNavigation);

  useEffect(() => {
    const controller = new AbortController();

    fetchNavigation(controller.signal)
      .then(setNavigation)
      .catch(() => setNavigation(defaultNavigation));

    return () => controller.abort();
  }, []);

  return (
    <footer className={compact ? "site-footer site-footer--compact" : "site-footer"}>
      <div className="site-footer__inner">
        <div className="site-footer__brand">
          <Link className="brand-mark" href="/">
            <KgmLogo />
          </Link>
          <p>Karacabey için hızlı market siparişi, güvenli ödeme ve kurumsal teslimat deneyimi.</p>
          <div className="site-footer__trust" aria-label="Guven sinyalleri">
            <span><ShieldCheck size={17} /> Güvenli alışveriş</span>
            <span><Truck size={17} /> Hızlı teslimat</span>
            <span><Tag size={17} /> Gross fırsatlar</span>
          </div>
        </div>
        <FooterColumn title="Alışveriş" items={navigation.footer_primary} />
        <FooterColumn title="Kurumsal" items={navigation.footer_corporate} />
        <FooterColumn title="Destek" items={navigation.footer_support} />
        <FooterColumn title="Hesap" items={navigation.footer_account} />
      </div>
    </footer>
  );
}

type FooterColumnProps = {
  title: string;
  items: NavigationItem[];
};

function FooterColumn({ title, items }: FooterColumnProps) {
  return (
    <nav className="site-footer__column" aria-label={title}>
      <strong>{title}</strong>
      {items.map((item) => (
        <Link key={`${item.label}-${item.url}`} href={item.url} target={item.external ? "_blank" : undefined} rel={item.external ? "noreferrer" : undefined}>
          <NavIcon name={item.icon} />
          {item.label}
        </Link>
      ))}
    </nav>
  );
}
