import type { Metadata } from "next";
import Link from "next/link";
import { AuthLayout } from "@/app/_layouts/AuthLayout";

export const metadata: Metadata = {
  title: "Kayıt",
  robots: {
    index: false,
    follow: false,
  },
};

export default function RegisterPage() {
  return (
    <AuthLayout>
      <section className="auth-panel">
        <p className="eyebrow">Yeni hesap</p>
        <h1>Karacabey Gross üyeliği oluşturun.</h1>
        <form className="form-stack" action={`${process.env.NEXT_PUBLIC_API_URL ?? ""}/api/v1/auth/register`} method="post">
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
          <input name="device_name" type="hidden" value="next-storefront" />
          <button className="primary-action" type="submit">
            Kayıt Ol
          </button>
        </form>
        <div className="split-actions">
          <Link className="secondary-action" href="/auth/login">
            Giriş Yap
          </Link>
          <Link className="secondary-action" href="/products">
            Ürünler
          </Link>
        </div>
      </section>
    </AuthLayout>
  );
}
