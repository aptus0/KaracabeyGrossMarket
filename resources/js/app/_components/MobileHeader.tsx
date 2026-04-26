import Link from "next/link";
import { SearchBar } from "@/app/_components/SearchBar";

export function MobileHeader() {
  return (
    <div className="mobile-header">
      <div className="mobile-header__top">
        <Link className="brand-mark" href="/">
          <span>Karacabey</span>
          <strong>Gross Market</strong>
        </Link>
        <Link className="header-action" href="/checkout">
          Sepet
        </Link>
      </div>
      <SearchBar compact />
    </div>
  );
}
