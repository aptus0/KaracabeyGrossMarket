import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Breadcrumb } from "@/app/_components/Breadcrumb";
import { SeoHead } from "@/app/_components/SeoHead";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { findStorePage, storePages } from "@/lib/content";

type CorporatePageProps = {
  params: Promise<{
    slug: string;
  }>;
};

export function generateStaticParams() {
  return storePages.map((page) => ({ slug: page.slug }));
}

export async function generateMetadata({ params }: CorporatePageProps): Promise<Metadata> {
  const { slug } = await params;
  const page = findStorePage(slug);

  if (!page) {
    return {};
  }

  return {
    title: page.seo.title,
    description: page.seo.description,
    alternates: {
      canonical: `/kurumsal/${page.slug}`,
    },
    openGraph: {
      title: page.seo.title,
      description: page.seo.description,
      type: "article",
    },
  };
}

export default async function CorporatePage({ params }: CorporatePageProps) {
  const { slug } = await params;
  const page = findStorePage(slug);

  if (!page) {
    notFound();
  }

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "WebPage",
    name: page.title,
    description: page.seo.description,
    url: `https://karacabeygrossmarket.com/kurumsal/${page.slug}`,
  };

  return (
    <GuestLayout>
      <SeoHead data={jsonLd} />
      <main className="content-page">
        <Breadcrumb
          items={[
            { href: "/", label: "Ana Sayfa" },
            { label: page.title },
          ]}
        />
        <article className="content-article">
          <p className="eyebrow">{page.group}</p>
          <h1>{page.title}</h1>
          <p>{page.body}</p>
        </article>
      </main>
    </GuestLayout>
  );
}
