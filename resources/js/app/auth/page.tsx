import Link from "next/link";
import { ShellHeader } from "@/app/_components/ShellHeader";

export default function AuthPage() {
  return (
    <>
      <ShellHeader />
      <main className="s50">
        <section className="s51">
          <p className="s7">Karacabey Gross hesabı</p>
          <h1>Hızlı giriş, güvenli checkout.</h1>
          <p className="s9">
            Siparişlerinizi, kayıtlı PayTR kart tokenlarınızı ve teslimat bilgilerinizi tek alanda yönetin.
          </p>
          <div className="s53">
            <Link className="s54" href="/auth/login">
              Giriş Yap
            </Link>
            <Link className="s55" href="/auth/register">
              Kayıt Ol
            </Link>
          </div>
        </section>
      </main>
    </>
  );
}
