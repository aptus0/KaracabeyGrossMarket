import Image from "next/image";
import Link from "next/link";
import type { Metadata } from "next";
import { SeoHead } from "@/app/_components/SeoHead";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { blogPosts } from "@/lib/blog";
import { buildMetadata, siteUrl } from "@/lib/seo";

export const metadata: Metadata = buildMetadata({
  title: "Blog",
  description: "Karacabey Gross Market blog içerikleri: teslimat, ödeme güvenliği, sipariş planlama, mobil alışveriş ve ürün rehberleri.",
  path: "/blog",
  keywords: ["blog", "sipariş planlama", "ödeme güvenliği", "mobil alışveriş", "market rehberi", "Karacabey blog"],
});

export default function BlogIndexPage() {
  const [featuredPost, ...posts] = blogPosts;
  const blogSchema = {
    "@context": "https://schema.org",
    "@type": "Blog",
    name: "Karacabey Gross Market Blog",
    description: "Online market deneyimi, teslimat akışı, ödeme güvenliği ve mobil alışveriş hakkında içerikler.",
    url: `${siteUrl}/blog`,
    inLanguage: "tr-TR",
    blogPost: blogPosts.map((post) => ({
      "@type": "BlogPosting",
      headline: post.title,
      description: post.excerpt,
      datePublished: post.publishedAt,
      articleSection: post.category,
      image: post.heroImage,
      url: `${siteUrl}/blog/${post.slug}`,
    })),
  };

  return (
    <GuestLayout>
      <SeoHead data={blogSchema} />
      <main className="content-band">
        <section className="mx-auto grid w-full max-w-[1180px] gap-8">
          <div className="grid gap-3">
            <p className="eyebrow">Blog</p>
            <h1 className="text-4xl font-bold text-[#2B2F36] sm:text-5xl">
              Online market deneyimini büyüten içerikler
            </h1>
            <p className="max-w-3xl text-base leading-8 text-[#6B7177]">
              Sipariş planlama, ödeme güvenliği, mobil alışveriş, ürün seçimi ve teslimat tarafında kullanıcıya gerçekten fayda sağlayan rehberleri burada topluyoruz.
            </p>
          </div>

          <div className="grid gap-5 lg:grid-cols-[minmax(0,1.08fr)_minmax(320px,0.92fr)]">
            <article className="overflow-hidden rounded-[32px] border border-[#E4E7EB] bg-white shadow-[0_20px_52px_rgba(43,47,54,0.08)]">
              <div className="relative aspect-[16/9]">
                <Image
                  src={featuredPost.heroImage}
                  alt={featuredPost.title}
                  fill
                  className="object-cover"
                  sizes="(max-width: 1200px) 100vw, 60vw"
                  priority
                />
              </div>
              <div className="grid gap-5 p-7 sm:p-8">
                <div className="flex flex-wrap gap-2 text-xs font-bold uppercase tracking-[0.14em] text-[#FF7A00]">
                  <span>{featuredPost.category}</span>
                  <span className="text-[#98A0A8]">{featuredPost.readTime}</span>
                  <span className="text-[#98A0A8]">{formatBlogDate(featuredPost.publishedAt)}</span>
                </div>
                <div className="grid gap-3">
                  <h2 className="text-3xl font-bold leading-tight text-[#2B2F36]">{featuredPost.title}</h2>
                  <p className="text-sm leading-7 text-[#6B7177]">{featuredPost.excerpt}</p>
                </div>
                <div className="flex flex-wrap gap-3">
                  <Link
                    href={`/blog/${featuredPost.slug}`}
                    className="inline-flex min-h-12 items-center justify-center rounded-2xl bg-[#FF7A00] px-5 text-sm font-bold text-white"
                  >
                    Yazıyı Aç
                  </Link>
                  <Link
                    href="/products"
                    className="inline-flex min-h-12 items-center justify-center rounded-2xl border border-[#E4E7EB] bg-[#F7F9FB] px-5 text-sm font-semibold text-[#2B2F36]"
                  >
                    Ürünleri İncele
                  </Link>
                </div>
              </div>
            </article>

            <aside className="grid gap-4 rounded-[32px] border border-[#E4E7EB] bg-white p-6 shadow-[0_20px_52px_rgba(43,47,54,0.05)]">
              <div className="grid gap-2">
                <p className="text-xs font-bold uppercase tracking-[0.14em] text-[#FF7A00]">SEO Odaklı İçerik Başlıkları</p>
                <h2 className="text-2xl font-bold leading-tight text-[#2B2F36]">Blog içinde aranan ana konular</h2>
                <p className="text-sm leading-7 text-[#6B7177]">
                  Online market siparişi, güvenli ödeme, hızlı teslimat, mobil alışveriş akışı ve ürün planlama gibi yüksek niyetli başlıklara odaklanıyoruz.
                </p>
              </div>
              <div className="grid gap-3">
                {[
                  "Karacabey online market önerileri",
                  "Güvenli ödeme ve 3D Secure rehberleri",
                  "Mobil sipariş ve hızlı checkout içerikleri",
                  "Teslimat, adres ve taze ürün planlama yazıları",
                ].map((topic) => (
                  <div
                    key={topic}
                    className="rounded-2xl border border-[#E4E7EB] bg-[#F7F9FB] px-4 py-3 text-sm font-medium leading-6 text-[#4D535A]"
                  >
                    {topic}
                  </div>
                ))}
              </div>
            </aside>
          </div>

          <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            {posts.map((post) => (
              <article
                key={post.slug}
                className="overflow-hidden rounded-[28px] border border-[#E4E7EB] bg-white shadow-[0_18px_48px_rgba(43,47,54,0.08)]"
              >
                <div className="relative aspect-[16/10]">
                  <Image src={post.heroImage} alt={post.title} fill className="object-cover" sizes="(max-width: 900px) 100vw, 50vw" />
                </div>
                <div className="grid gap-4 p-6">
                  <div className="flex flex-wrap gap-2 text-xs font-black uppercase tracking-[0.12em] text-[#FF7A00]">
                    <span>{post.category}</span>
                    <span className="text-[#98A0A8]">{post.readTime}</span>
                  </div>
                  <div className="grid gap-3">
                    <h2 className="text-2xl font-black leading-tight text-[#2B2F36]">{post.title}</h2>
                    <p className="text-sm leading-7 text-[#6B7177]">{post.excerpt}</p>
                  </div>
                  <div className="flex items-center justify-between gap-3">
                    <span className="text-sm font-semibold text-[#98A0A8]">{formatBlogDate(post.publishedAt)}</span>
                    <Link
                      href={`/blog/${post.slug}`}
                      className="inline-flex min-h-11 items-center justify-center rounded-2xl bg-[#FF7A00] px-4 text-sm font-black text-white"
                    >
                      Yazıyı Aç
                    </Link>
                  </div>
                </div>
              </article>
            ))}
          </div>

          <section className="grid gap-4 rounded-[32px] border border-[#E4E7EB] bg-white p-7 shadow-[0_18px_48px_rgba(43,47,54,0.05)] md:grid-cols-3">
            {[
              {
                title: "Sipariş Planlama",
                description: "Haftalık market listesi, tekrar sipariş ve favori ürün akışı üzerine daha çok içerik üretiyoruz.",
              },
              {
                title: "Mobil Alışveriş",
                description: "İki kolon ürün listesi, hızlı ürün detayı ve kısa checkout deneyimi için pratik rehberler burada toplanıyor.",
              },
              {
                title: "Güvenli Ödeme",
                description: "SSL, 3D Secure, sipariş özeti ve güven sinyalleri üzerinden dönüşüm kalitesini artıran notlar yayınlıyoruz.",
              },
            ].map((item) => (
              <div key={item.title} className="grid gap-2 rounded-[24px] bg-[#F7F9FB] p-5">
                <h3 className="text-lg font-bold text-[#2B2F36]">{item.title}</h3>
                <p className="text-sm leading-7 text-[#6B7177]">{item.description}</p>
              </div>
            ))}
          </section>
        </section>
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
