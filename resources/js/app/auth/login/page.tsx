import type { Metadata } from "next";
import Link from "next/link";
import { AuthLayout } from "@/app/_layouts/AuthLayout";

export const metadata: Metadata = {
  title: "Giriş",
  robots: {
    index: false,
    follow: false,
  },
};

export default function LoginPage() {
  return (
    <AuthLayout>
      <section className="auth-panel">
        <p className="eyebrow">Hesabım</p>
        <h1>Giriş yapın.</h1>
        <form className="form-stack" action={`${process.env.NEXT_PUBLIC_API_URL ?? ""}/api/v1/auth/login`} method="post">
          <label>
            E-posta
            <input name="email" type="email" autoComplete="email" required />
          </label>
          <label>
            Şifre
            <input name="password" type="password" autoComplete="current-password" required />
          </label>
          <input name="device_name" type="hidden" value="next-storefront" />
          <button className="primary-action" type="submit">
            Giriş Yap
          </button>
        </form>
        <div className="split-actions">
          <Link className="secondary-action" href="/auth/register">
            Kayıt Ol
          </Link>
          <Link className="secondary-action" href="/checkout">
            Checkout
          </Link>
        </div>
      </section>
    </AuthLayout>
  );
}
