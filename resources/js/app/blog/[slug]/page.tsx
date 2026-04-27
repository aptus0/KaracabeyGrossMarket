import Image from "next/image";
import Link from "next/link";
import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { SeoHead } from "@/app/_components/SeoHead";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { blogPosts, findBlogPost } from "@/lib/blog";
import { buildMetadata, siteUrl } from "@/lib/seo";

type BlogDetailPageProps = {
  params: Promise<{
    slug: string;
  }>;
};

export function generateStaticParams() {
  return blogPosts.map((post) => ({ slug: post.slug }));
}

export async function generateMetadata({ params }: BlogDetailPageProps): Promise<Metadata> {
  const { slug } = await params;
  const post = findBlogPost(slug);

  if (!post) {
    return {};
  }

  return {
    ...buildMetadata({
      title: post.seo.title,
      description: post.seo.description,
      path: `/blog/${post.slug}`,
      image: post.heroImage,
      type: "article",
      keywords: [...post.seo.keywords, post.category, post.readTime, "blog detayı", "Karacabey Gross Market"],
    }),
  };
}

export default async function BlogDetailPage({ params }: BlogDetailPageProps) {
  const { slug } = await params;
  const post = findBlogPost(slug);

  if (!post) {
    notFound();
  }

  const articleSchema = {
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    headline: post.title,
    description: post.excerpt,
    datePublished: post.publishedAt,
    dateModified: post.publishedAt,
    inLanguage: "tr-TR",
    articleSection: post.category,
    image: post.heroImage,
    keywords: post.seo.keywords.join(", "),
    mainEntityOfPage: `${siteUrl}/blog/${post.slug}`,
    publisher: {
      "@type": "Organization",
      name: "Karacabey Gross Market",
      url: siteUrl,
    },
  };

  return (
    <GuestLayout>
      <SeoHead data={articleSchema} />
      <main className="content-band">
        <article className="mx-auto grid w-full max-w-[980px] gap-8">
          <Link href="/blog" className="text-sm font-black uppercase tracking-[0.14em] text-[#FF7A00]">
            Blog&apos;a Dön
          </Link>

          <div className="grid gap-4">
            <div className="flex flex-wrap gap-2 text-xs font-black uppercase tracking-[0.12em] text-[#FF7A00]">
              <span>{post.category}</span>
              <span className="text-[#98A0A8]">{post.readTime}</span>
              <span className="text-[#98A0A8]">{formatBlogDate(post.publishedAt)}</span>
            </div>
            <h1 className="text-4xl font-black leading-tight text-[#2B2F36] sm:text-5xl">{post.title}</h1>
            <p className="max-w-3xl text-base leading-8 text-[#6B7177]">{post.excerpt}</p>
          </div>

          <div className="relative aspect-[16/9] overflow-hidden rounded-[32px] border border-[#E4E7EB] bg-[#F6F7F8]">
            <Image src={post.heroImage} alt={post.title} fill className="object-cover" sizes="100vw" priority />
          </div>

          <div className="grid gap-5 rounded-[28px] border border-[#E4E7EB] bg-white p-8 shadow-[0_18px_48px_rgba(43,47,54,0.06)]">
            {post.content.map((paragraph) => (
              <p key={paragraph} className="text-base leading-8 text-[#4D535A]">
                {paragraph}
              </p>
            ))}
          </div>
        </article>
      </main>
    </GuestLayout>
  );
}

function formatBlogDate(value: string) {
  return new Intl.DateTimeFormat("tr-TR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  }).format(new Date(value));
}
