import React from "react";
import Link from "next/link";
import type { KgmCategory } from "@/lib/catalog";

type CategoryNavigationProps = {
  categories?: KgmCategory[];
};

// Category icons with emojis for visual consistency
const categoryIcons: Record<string, string> = {
  "temel-gida": "🛒",
  "meyve-sebze": "🍎",
  "sarkuteri": "🥩",
  "atistirmali": "🍊",
  "zucaciye": "🍳",
  "hirdavat": "🔨",
  "kozmetik-bakim": "💅",
  "bavul-seyahat": "🧳",
};

const defaultCategories = [
  { name: "Temel Gıda", slug: "temel-gida" },
  { name: "Meyve & Sebze", slug: "meyve-sebze" },
  { name: "Şarküteri", slug: "sarkuteri" },
  { name: "Atıştırmalık", slug: "atistirmali" },
  { name: "Zücaciye", slug: "zucaciye" },
  { name: "Hırdavat", slug: "hirdavat" },
  { name: "Kozmetik & Bakım", slug: "kozmetik-bakim" },
  { name: "Bavul & Seyahat", slug: "bavul-seyahat" },
];

export function CategoryNavigation({ categories }: CategoryNavigationProps) {
  const displayCategories = categories?.length ? categories.slice(0, 8) : defaultCategories;

  return (
    <section className="max-w-[1120px] mx-auto px-4 md:px-6 py-6">
      <div className="grid grid-cols-4 md:grid-cols-8 gap-3 md:gap-4">
        {displayCategories.map((category) => {
          const slug = (category as any).slug;
          const name = (category as any).name;
          const icon = categoryIcons[slug] || "📦";
          const href = `/products?category=${encodeURIComponent(slug)}`;

          return (
            <Link
              key={slug}
              href={href}
              className="flex flex-col items-center justify-center gap-3 p-4 rounded-2xl bg-white hover:shadow-lg transition-all duration-300 hover:scale-105 border border-gray-200"
            >
              <div className="text-4xl">{icon}</div>
              <span className="text-xs md:text-sm font-semibold text-center line-clamp-2 text-gray-900">
                {name}
              </span>
            </Link>
          );
        })}
      </div>
    </section>
  );
}
