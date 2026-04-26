import type { ReactNode } from "react";
import { BottomNavigation } from "@/app/_components/BottomNavigation";
import { Footer } from "@/app/_components/Footer";
import { Header } from "@/app/_components/Header";

type GuestLayoutProps = {
  children: ReactNode;
};

export function GuestLayout({ children }: GuestLayoutProps) {
  return (
    <>
      <Header />
      {children}
      <Footer />
      <BottomNavigation />
    </>
  );
}
