import type { ReactNode } from "react";
import { AccountSidebar } from "@/app/_components/AccountSidebar";
import { BottomNavigation } from "@/app/_components/BottomNavigation";
import { Footer } from "@/app/_components/Footer";
import { Header } from "@/app/_components/Header";

type AppLayoutProps = {
  children: ReactNode;
  sidebar?: boolean;
};

export function AppLayout({ children, sidebar = false }: AppLayoutProps) {
  return (
    <>
      <Header />
      <main className={sidebar ? "app-shell app-shell--with-sidebar" : "app-shell"}>
        {sidebar ? <AccountSidebar /> : null}
        <div className="app-shell__content">{children}</div>
      </main>
      <Footer compact />
      <BottomNavigation />
    </>
  );
}
