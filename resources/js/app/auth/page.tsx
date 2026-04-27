import Link from "next/link";
import { AuthLayout } from "@/app/_layouts/AuthLayout";

export default function AuthPage() {
  return (
    <AuthLayout>
      <section className="auth-panel">
        <p className="eyebrow">Karacabey Gross hesabı</p>
        <h1>Hızlı giriş, güvenli checkout.</h1>
        <p>
          Siparişlerinizi, kayıtlı kartlarınızı ve teslimat bilgilerinizi tek alanda yönetin.
        </p>
        <div className="split-actions">
          <Link className="primary-action" href="/auth/login">
            Giriş Yap
          </Link>
          <Link className="secondary-action" href="/auth/register">
            Kayıt Ol
          </Link>
        </div>
      </section>
    </AuthLayout>
  );
}
