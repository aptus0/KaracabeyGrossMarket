"use client";

type GlobalErrorProps = {
  error: Error & {
    digest?: string;
  };
  reset: () => void;
};

export default function GlobalError({ error, reset }: GlobalErrorProps) {
  return (
    <html lang="tr">
      <body className="s0">
        <main className="content-page">
          <section className="content-article">
            <p className="eyebrow">Sistem Bildirimi</p>
            <h1>Bir şeyler ters gitti</h1>
            <p>
              Sayfa yüklenirken beklenmeyen bir hata oluştu. Hemen tekrar
              deneyebilir veya ana sayfaya dönebilirsiniz.
            </p>
            {error.message ? (
              <p className="text-sm text-[#6B7177]">{error.message}</p>
            ) : null}
            <div className="flex flex-wrap gap-3 pt-2">
              <button type="button" className="primary-action" onClick={() => reset()}>
                Tekrar Dene
              </button>
              <a href="/" className="secondary-action">
                Ana Sayfa
              </a>
            </div>
          </section>
        </main>
      </body>
    </html>
  );
}
