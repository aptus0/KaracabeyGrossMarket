"use client";

import Image from "next/image";
import Link from "next/link";
import { type ReactNode, useEffect, useState } from "react";
import {
  ArrowUpRight,
  CreditCard,
  Lock,
  Mail,
  MapPin,
  MessageCircle,
  Phone,
  ShieldCheck,
  Tag,
  Truck,
} from "lucide-react";
import { KgmLogo } from "@/app/_components/KgmLogo";
import { NavIcon } from "@/app/_components/NavIcon";
import { Button } from "@/app/_components/ui/button";
import { Separator } from "@/app/_components/ui/separator";
import { blogPosts } from "@/lib/blog";
import {
  defaultNavigation,
  fetchNavigation,
  type NavigationData,
  type NavigationItem,
} from "@/lib/navigation";

type FooterProps = {
  compact?: boolean;
};

const cardLogos = [
  { name: "Visa", src: "/assets/brands/payment/visa.svg" },
  { name: "Mastercard", src: "/assets/brands/payment/mastercard.svg" },
  { name: "American Express", src: "/assets/brands/payment/amex.svg" },
  { name: "Bankkart", src: "/assets/brands/payment/bankkart.svg" },
];

const mealCardLogos = [
  { name: "Sodexo", src: "/assets/brands/meal/sodexo.svg" },
  { name: "Multinet", src: "/assets/brands/meal/multinet.svg" },
  { name: "Edenred Ticket", src: "/assets/brands/meal/edenred-ticket.svg" },
  { name: "MetropolCard", src: "/assets/brands/meal/metropol.svg" },
];

const footerNotes = [
  {
    icon: <Truck size={20} />,
    title: "Planlı teslimat operasyonu",
    description: "Karacabey merkez ve yakın bölgelerde düzenli sipariş akışı için tasarlandı.",
  },
  {
    icon: <ShieldCheck size={20} />,
    title: "Güvenli ödeme katmanı",
    description: "SSL, 3D Secure ve PayTR entegrasyonu ile checkout yüzeyi korunur.",
  },
  {
    icon: <Tag size={20} />,
    title: "Kurumsal ve bireysel kullanım",
    description: "Tekrarlı siparişler, kampanyalar ve hesap yönetimi aynı yapı içinde ilerler.",
  },
];

const whatsappDisplay = "065453458663";
const whatsappUrl = "https://wa.me/9065453458663";
const phoneDisplay = "(0224) 676 84 33";
const phoneUrl = "tel:+902246768433";
const addressDisplay = "Drama, Runguçpaşa Caddesi, 16700 Karacabey/Bursa";
const mapsUrl =
  "https://www.google.com/maps/search/?api=1&query=Drama%2C%20Rungu%C3%A7pa%C5%9Fa%20Caddesi%2C%2016700%20Karacabey%2FBursa";

export function Footer({ compact = false }: FooterProps) {
  const [navigation, setNavigation] = useState<NavigationData>(defaultNavigation);
  const [email, setEmail] = useState("");

  useEffect(() => {
    const controller = new AbortController();
    fetchNavigation(controller.signal)
      .then(setNavigation)
      .catch(() => setNavigation(defaultNavigation));
    return () => controller.abort();
  }, []);

  const blogLinks = blogPosts.slice(0, 4).map((post) => ({
    label: post.title,
    url: `/blog/${post.slug}`,
    icon: "file-text" as const,
  }));

  return (
    <footer className={compact ? "site-footer site-footer--compact" : "site-footer"}>
      <div className={`mx-auto w-full max-w-[1440px] px-6 sm:px-8 lg:px-12 ${compact ? "py-12" : "py-16"}`}>
        <div className="grid gap-8">
          <div className="grid gap-8 lg:grid-cols-4 lg:gap-12">
            <section className="grid content-start gap-6">
              <Link className="inline-flex w-fit rounded-2xl border border-[#E4E7EB] bg-white p-4 shadow-sm" href="/">
                <KgmLogo />
              </Link>

              <div className="grid gap-3">
                <p className="text-xs font-black uppercase tracking-[0.2em] text-[#6B7177]">
                  Karacabey Gross Market
                </p>
                <h2 className="text-2xl font-black leading-tight text-[#111827]">
                  Karacabey için güvenilir online market operasyonu.
                </h2>
                <p className="text-sm leading-7 text-[#6B7177]">
                  Hızlı teslimat, güvenli ödeme, kurumsal sipariş akışı ve modern hesap deneyimi.
                </p>
              </div>

              <div className="flex flex-wrap gap-3">
                <Button asChild className="h-11 rounded-xl px-6">
                  <Link href="/products">Ürünleri İncele</Link>
                </Button>
                <Button asChild variant="outline" className="h-11 rounded-xl border-[#E4E7EB] px-6">
                  <Link href="/kurumsal/hakkimizda">Kurumsal Bilgiler</Link>
                </Button>
              </div>
            </section>

            <section className="grid content-start gap-8">
              <FooterColumn
                title="Alışveriş"
                items={[
                  ...navigation.footer_primary,
                  { label: "Kampanyalar", url: "/kampanyalar", icon: "tag" },
                  { label: "Yeni Gelen Ürünler", url: "/products?sort=newest", icon: "grid" },
                ]}
              />
              <FooterColumn
                title="Kurumsal"
                items={[
                  ...navigation.footer_corporate,
                  { label: "Ödeme Güvenliği", url: "/kurumsal/odeme-guvenligi", icon: "shield" },
                  { label: "Teslimat Bölgeleri", url: "/kurumsal/teslimat-bolgeleri", icon: "map-pin" },
                  { label: "Mesafeli Satış Sözleşmesi", url: "/kurumsal/mesafeli-satis-sozlesmesi", icon: "file-text" },
                ]}
              />
            </section>

            <section className="grid content-start gap-8">
              <FooterColumn
                title="Destek"
                items={[
                  ...navigation.footer_support,
                  { label: "Üyelik Sözleşmesi", url: "/kurumsal/uyelik-sozlesmesi", icon: "file-text" },
                  { label: "Gizlilik Politikası", url: "/kurumsal/gizlilik-politikasi", icon: "shield" },
                  { label: "İletişim", url: "/kurumsal/iletisim", icon: "phone" },
                  { label: "KVKK", url: "/kurumsal/kvkk", icon: "shield" },
                ]}
              />
            </section>

            <section className="grid content-start gap-8">
              <FooterColumn
                title="Hesabım & Blog"
                items={[
                  ...navigation.footer_account,
                  { label: "Blog Ana Sayfa", url: "/blog", icon: "file-text" },
                  ...blogLinks,
                ]}
              />
            </section>
          </div>

          <Separator className="bg-[#E4E7EB]" />

          <div className="grid gap-4 rounded-2xl border border-[#E4E7EB] bg-[#FAFBFC] p-6 lg:grid-cols-[0.95fr_1fr_1fr_1.2fr] lg:gap-5 lg:p-8">
            <div className="grid content-start gap-3">
              <div className="inline-flex items-center gap-2 text-[#FF7A00]">
                <Phone size={18} />
                <span className="text-xs font-black uppercase tracking-[0.16em]">İletişim</span>
              </div>
              <h3 className="text-xl font-black text-[#111827]">Bize hızlıca ulaşın</h3>
              <p className="text-sm leading-6 text-[#6B7177]">
                WhatsApp, telefon ve adres bilgilerimiz footer içinde her zaman görünür olsun istediğin gibi ekledim.
              </p>
              <div className="flex flex-wrap gap-3 pt-1">
                <Button asChild variant="outline" className="h-11 rounded-xl border-[#E4E7EB] px-5">
                  <Link href="/kurumsal/iletisim">İletişim Sayfası</Link>
                </Button>
                <Button asChild className="h-11 rounded-xl px-5">
                  <Link href={mapsUrl} target="_blank" rel="noreferrer">
                    Google Maps Konum
                    <ArrowUpRight size={15} />
                  </Link>
                </Button>
              </div>
            </div>

            <ContactCard
              href={whatsappUrl}
              icon={<MessageCircle size={18} />}
              label="WhatsApp"
              value={whatsappDisplay}
              description="Yazılı iletişim ve hızlı bilgi için."
              external
            />
            <ContactCard
              href={phoneUrl}
              icon={<Phone size={18} />}
              label="Telefon"
              value={phoneDisplay}
              description="Sipariş ve destek hattı."
            />
            <ContactCard
              href={mapsUrl}
              icon={<MapPin size={18} />}
              label="Adres"
              value={addressDisplay}
              description="Drama, Runguçpaşa Caddesi üzerinde."
              external
            />
          </div>

          <Separator className="bg-[#E4E7EB]" />

          <div className="rounded-2xl border border-[#E4E7EB] bg-[#FAFBFC] p-8 lg:p-10">
            <div className="flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
              <div className="grid max-w-xl gap-3">
              <div className="inline-flex items-center gap-2 text-[#FF7A00]">
                <Mail size={20} />
                <span className="text-xs font-black uppercase tracking-[0.16em]">Bülten</span>
              </div>
              <h3 className="text-xl font-black text-[#111827]">Kampanyalardan ilk sen haberdar ol</h3>
              <p className="text-sm leading-6 text-[#6B7177]">
                Yeni ürünler, indirimler ve özel fırsatlar için e-posta listemize katıl. Spam yok, sadece değerli
                içerik.
              </p>
            </div>
              <form
                className="flex w-full gap-3 lg:w-auto"
                onSubmit={(event) => {
                  event.preventDefault();
                  setEmail("");
                }}
              >
                <input
                  type="email"
                  value={email}
                  onChange={(event) => setEmail(event.target.value)}
                  placeholder="E-posta adresiniz"
                  className="h-12 w-full rounded-xl border border-[#E4E7EB] bg-white px-5 text-sm text-[#111827] placeholder:text-[#9AA3AF] focus:border-[#FF7A00] focus:outline-none focus:ring-2 focus:ring-[#FF7A00]/10 lg:w-80"
                  required
                />
                <Button type="submit" className="h-12 rounded-xl px-8 whitespace-nowrap">
                  Abone Ol
                </Button>
              </form>
            </div>
          </div>

          <Separator className="bg-[#E4E7EB]" />

          <div className="grid gap-4 sm:grid-cols-3">
            {footerNotes.map((item) => (
              <div
                key={item.title}
                className="flex items-start gap-4 rounded-2xl border border-[#E4E7EB] bg-white p-6"
              >
                <div className="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#FFF3E6] text-[#FF7A00]">
                  {item.icon}
                </div>
                <div className="grid gap-1">
                  <strong className="text-sm font-black text-[#111827]">{item.title}</strong>
                  <p className="text-sm leading-6 text-[#6B7177]">{item.description}</p>
                </div>
              </div>
            ))}
          </div>

          <Separator className="bg-[#E4E7EB]" />

          <div className="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div className="flex flex-wrap items-center gap-4">
              <span className="text-xs font-black uppercase tracking-[0.14em] text-[#6B7177]">Ödeme</span>
              <div className="flex items-center gap-2">
                {cardLogos.map((card) => (
                  <div
                    key={card.name}
                    className="flex h-9 items-center justify-center rounded-lg border border-[#E4E7EB] bg-white px-3"
                    title={card.name}
                  >
                    <Image
                      src={card.src}
                      alt={card.name}
                      width={40}
                      height={24}
                      className="h-auto max-h-5 w-auto"
                    />
                  </div>
                ))}
              </div>

              <span className="text-xs font-black uppercase tracking-[0.14em] text-[#6B7177]">Yemek</span>
              <div className="flex items-center gap-2">
                {mealCardLogos.map((card) => (
                  <div
                    key={card.name}
                    className="flex h-9 items-center justify-center rounded-lg border border-[#E4E7EB] bg-white px-3"
                    title={card.name}
                  >
                    <Image
                      src={card.src}
                      alt={card.name}
                      width={40}
                      height={24}
                      className="h-auto max-h-5 w-auto"
                    />
                  </div>
                ))}
              </div>
            </div>

            <div className="flex items-center gap-4">
              <span className="text-xs font-black uppercase tracking-[0.14em] text-[#6B7177]">Güvenlik</span>
              <div className="flex items-center gap-2">
                {[
                  { name: "SSL", icon: <Lock size={14} /> },
                  { name: "256-bit", icon: <ShieldCheck size={14} /> },
                  { name: "3D Secure", icon: <CreditCard size={14} /> },
                ].map((item) => (
                  <div
                    key={item.name}
                    className="inline-flex items-center gap-1.5 rounded-lg border border-[#E4E7EB] bg-white px-3 py-2 text-[#6B7177]"
                    title={item.name}
                  >
                    <span className="text-[#FF7A00]">{item.icon}</span>
                    <span className="text-xs font-semibold">{item.name}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>

          <Separator className="bg-[#E4E7EB]" />

          <div className="flex flex-col gap-4 text-sm text-[#6B7177] lg:flex-row lg:items-center lg:justify-between">
            <p>© 2026 Karacabey Gross Market. Sipariş, ödeme ve teslimat yüzeyleri profesyonel şekilde korunur.</p>
            <div className="flex flex-wrap gap-6">
              <Link href="/kurumsal/kvkk" className="transition-colors hover:text-[#111827]">
                KVKK
              </Link>
              <Link href="/kurumsal/mesafeli-satis-sozlesmesi" className="transition-colors hover:text-[#111827]">
                Mesafeli Satış
              </Link>
              <Link href="/kurumsal/odeme-guvenligi" className="transition-colors hover:text-[#111827]">
                Ödeme Güvenliği
              </Link>
              <Link href="/kurumsal/iletisim" className="transition-colors hover:text-[#111827]">
                İletişim
              </Link>
              <Link href="/blog" className="transition-colors hover:text-[#111827]">
                Blog
              </Link>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}

type FooterColumnProps = {
  title: string;
  items: Array<NavigationItem | { label: string; url: string; icon?: string | null }>;
};

function FooterColumn({ title, items }: FooterColumnProps) {
  return (
    <nav className="grid content-start gap-4" aria-label={title}>
      <strong className="text-sm font-black uppercase tracking-[0.16em] text-[#111827]">{title}</strong>
      <div className="grid gap-2">
        {items.map((item) => (
          <Link
            key={`${item.label}-${item.url}`}
            href={item.url}
            target={"external" in item && item.external ? "_blank" : undefined}
            rel={"external" in item && item.external ? "noreferrer" : undefined}
            className="inline-flex items-start gap-2.5 rounded-lg px-1 py-1.5 text-sm font-semibold text-[#6B7177] transition hover:bg-[#F3F4F6] hover:text-[#111827]"
          >
            <span className="mt-0.5 text-[#9AA3AF]">
              <NavIcon name={item.icon} size={16} />
            </span>
            <span className="line-clamp-2">{item.label}</span>
          </Link>
        ))}
      </div>
    </nav>
  );
}

function ContactCard({
  href,
  icon,
  label,
  value,
  description,
  external = false,
}: {
  href: string;
  icon: ReactNode;
  label: string;
  value: string;
  description: string;
  external?: boolean;
}) {
  return (
    <Link
      href={href}
      target={external ? "_blank" : undefined}
      rel={external ? "noreferrer" : undefined}
      className="grid content-start gap-3 rounded-2xl border border-[#E4E7EB] bg-white p-5 transition hover:border-[#FFD7B5] hover:bg-[#FFF9F2]"
    >
      <div className="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#FFF3E6] text-[#FF7A00]">
        {icon}
      </div>
      <div className="grid gap-1">
        <span className="text-xs font-black uppercase tracking-[0.16em] text-[#6B7177]">{label}</span>
        <strong className="text-sm font-black leading-6 text-[#111827]">{value}</strong>
        <span className="text-sm leading-6 text-[#6B7177]">{description}</span>
      </div>
    </Link>
  );
}
