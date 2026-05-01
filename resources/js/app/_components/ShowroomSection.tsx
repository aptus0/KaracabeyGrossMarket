import React from "react";
import { ProductCard } from "./ProductCard";
import type { KgmProduct } from "@/lib/catalog";
import { ChevronRight } from "lucide-react";
import Link from "next/link";

type ShowroomSectionProps = {
  title: string;
  subtitle: string;
  products: KgmProduct[];
  theme?: "default" | "cosmetics" | "bestseller";
  actionLink?: string;
  actionText?: string;
};

export function ShowroomSection({
  title,
  subtitle,
  products,
  theme = "default",
  actionLink,
  actionText = "Tümünü Gör",
}: ShowroomSectionProps) {
  if (!products || products.length === 0) return null;

  // Farklı konseptlere göre renk paletleri ve efektler
  const themeClasses = {
    default: "bg-gradient-to-br from-[#f8fafc] to-[#eef2f6]",
    cosmetics: "bg-gradient-to-br from-[#fff0f5] to-[#ffe4e1] border-[#ffb6c1]/30", // Soft pembe
    bestseller: "bg-gradient-to-br from-[#fffbeb] to-[#fef3c7] border-[#fde68a]/40", // Soft altın/sarı
  };

  return (
    <section className={`py-12 px-4 md:px-8 my-8 rounded-3xl relative overflow-hidden glass-panel ${themeClasses[theme]}`}>
      {/* Dekoratif arkaplan bulanıklıkları */}
      <div className="absolute top-0 right-0 w-64 h-64 bg-white/40 blur-3xl rounded-full -translate-y-1/2 translate-x-1/4 pointer-events-none" />
      <div className="absolute bottom-0 left-0 w-64 h-64 bg-white/40 blur-3xl rounded-full translate-y-1/3 -translate-x-1/4 pointer-events-none" />

      <div className="max-w-[1120px] mx-auto relative z-10">
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
          <div>
            <h2 className="showroom-title">{title}</h2>
            <p className="showroom-subtitle">{subtitle}</p>
          </div>
          
          {actionLink && (
            <Link 
              href={actionLink}
              className="inline-flex items-center gap-1 text-sm font-bold text-kgm-orange hover:text-orange-700 transition-colors bg-white/60 px-4 py-2 rounded-full shadow-sm backdrop-blur-md"
            >
              {actionText} <ChevronRight size={16} />
            </Link>
          )}
        </div>

        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
          {products.slice(0, 4).map((product) => (
            <div key={product.slug} className="transform transition-transform duration-300 hover:-translate-y-2">
              <ProductCard product={product} />
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
