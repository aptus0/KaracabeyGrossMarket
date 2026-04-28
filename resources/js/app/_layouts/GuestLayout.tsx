import type { ReactNode } from "react";
import { BottomNavigation } from "@/app/_components/BottomNavigation";
import { Footer } from "@/app/_components/Footer";
import { Header } from "@/app/_components/Header";
import { MobileHeader } from "@/app/_components/MobileHeader";

type GuestLayoutProps = {
  children: ReactNode;
};

export function GuestLayout({ children }: GuestLayoutProps) {
  return (
    <>
      {/* Desktop header — hidden on mobile via CSS */}
      <Header />
      {/* Mobile header — visible only on mobile via CSS */}
      <MobileHeader />
      {children}
      <Footer />
      <BottomNavigation />
    </>
  );
}
