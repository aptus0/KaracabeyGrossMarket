import type { ReactNode } from "react";
import { Footer } from "@/app/_components/Footer";
import { Header } from "@/app/_components/Header";

type AuthLayoutProps = {
  children: ReactNode;
};

export function AuthLayout({ children }: AuthLayoutProps) {
  return (
    <>
      <Header compact />
      <main className="auth-shell">{children}</main>
      <Footer compact />
    </>
  );
}
