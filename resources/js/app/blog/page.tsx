import Image from "next/image";
import Link from "next/link";
import type { Metadata } from "next";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { blogPosts } from "@/lib/blog";

export const metadata: Metadata = {
  title: "Blog",
  description: "Karacabey Gross Market blog içerikleri: teslimat, ödeme güvenliği, sipariş planlama ve mobil alışveriş akışları.",
};

export default function BlogIndexPage() {
  return (
    <GuestLayout>
      <main className="content-band">
        <section className="mx-auto grid w-full max-w-[1180px] gap-8">
          <div className="grid gap-3">
            <p className="eyebrow">Blog</p>
            <h1 className="text-4xl font-black text-[#2B2F36] sm:text-5xl">
              Online market akışını daha rahat hale getiren notlar
            </h1>
            <p className="max-w-3xl text-base leading-8 text-[#6B7177]">
              Sipariş planlama, ödeme güvenliği, mobil alışveriş ve teslimat tarafında işimize yarayan pratik içerikleri bir araya getirdik.
            </p>
          </div>

          <div className="grid gap-5 md:grid-cols-2">
            {blogPosts.map((post) => (
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
