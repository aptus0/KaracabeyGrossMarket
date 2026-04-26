import type { Metadata } from "next";
import Link from "next/link";
import { ShellHeader } from "@/app/_components/ShellHeader";

export const metadata: Metadata = {
  title: "Giriş",
  robots: {
    index: false,
    follow: false,
  },
};

export default function LoginPage() {
  return (
    <>
      <ShellHeader />
      <main className="s50">
        <section className="s51">
          <p className="s7">Hesabım</p>
          <h1>Giriş yapın.</h1>
          <form className="s52" action={`${process.env.NEXT_PUBLIC_API_URL ?? ""}/api/v1/auth/login`} method="post">
            <label>
              E-posta
              <input name="email" type="email" autoComplete="email" required />
            </label>
            <label>
              Şifre
              <input name="password" type="password" autoComplete="current-password" required />
            </label>
            <button className="s39" type="submit">
              Giriş Yap
            </button>
          </form>
          <div className="s53">
            <Link className="s55" href="/auth/register">
              Kayıt Ol
            </Link>
            <Link className="s55" href="/checkout">
              Checkout
            </Link>
          </div>
        </section>
      </main>
    </>
  );
}
