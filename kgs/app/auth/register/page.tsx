import type { Metadata } from "next";
import Link from "next/link";
import { ShellHeader } from "@/app/_components/ShellHeader";

export const metadata: Metadata = {
  title: "Kayıt",
  robots: {
    index: false,
    follow: false,
  },
};

export default function RegisterPage() {
  return (
    <>
      <ShellHeader />
      <main className="s50">
        <section className="s51">
          <p className="s7">Yeni hesap</p>
          <h1>Karacabey Gross üyeliği oluşturun.</h1>
          <form className="s52" action={`${process.env.NEXT_PUBLIC_API_URL ?? ""}/api/v1/auth/register`} method="post">
            <label>
              Ad Soyad
              <input name="name" autoComplete="name" required />
            </label>
            <label>
              E-posta
              <input name="email" type="email" autoComplete="email" required />
            </label>
            <label>
              Şifre
              <input name="password" type="password" autoComplete="new-password" required />
            </label>
            <button className="s39" type="submit">
              Kayıt Ol
            </button>
          </form>
          <div className="s53">
            <Link className="s55" href="/auth/login">
              Giriş Yap
            </Link>
            <Link className="s55" href="/products">
              Ürünler
            </Link>
          </div>
        </section>
      </main>
    </>
  );
}
