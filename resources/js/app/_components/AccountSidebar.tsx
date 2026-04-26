import Link from "next/link";

const accountLinks = [
  ["Hesap Özeti", "/account"],
  ["Adreslerim", "/account#addresses"],
  ["Siparişlerim", "/account#orders"],
  ["Kuponlarım", "/account#coupons"],
];

export function AccountSidebar() {
  return (
    <aside className="account-sidebar" aria-label="Hesap menüsü">
      {accountLinks.map(([label, href]) => (
        <Link key={href} href={href}>
          {label}
        </Link>
      ))}
    </aside>
  );
}
