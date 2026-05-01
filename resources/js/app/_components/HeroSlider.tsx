"use client";

import React from "react";
import { ChevronLeft, ChevronRight, Sparkles } from "lucide-react";
import Link from "next/link";

export function HeroSlider() {
  const [currentSlide, setCurrentSlide] = React.useState(0);

  const slides = [
    {
      title: "Haftalık Gross Fırsatları",
      subtitle: "Temel gıda, kahvaltılık ve günlük ihtiyaçlarda avantajlı sepet ürünleri.",
      tagline: "Taptaze • Ekonomik • Güvenilir",
      priceTag: "Her Gün\nEn Taze\nEn Uygun\nFiyatlar!",
      image: "https://images.unsplash.com/photo-1542838132-92c53300491e?w=1200&q=80",
    },
  ];

  const slide = slides[currentSlide];

  const handlePrev = () => {
    setCurrentSlide((prev) => (prev - 1 + slides.length) % slides.length);
  };

  const handleNext = () => {
    setCurrentSlide((prev) => (prev + 1) % slides.length);
  };

  return (
    <section className="relative mx-auto max-w-[1440px] px-4 md:px-12 py-8">
      <div className="relative overflow-hidden rounded-[40px] bg-[#FFF8F0] shadow-sm">
        <div className="grid min-h-[520px] grid-cols-1 md:grid-cols-2">
          {/* Left Content */}
          <div className="flex flex-col justify-center p-8 md:p-16">
            <div className="mb-6 inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2 text-xs font-bold text-[#16A34A] backdrop-blur-sm">
              <Sparkles size={14} />
              {slide.tagline}
            </div>
            <h1 className="mb-6 text-4xl font-black leading-tight text-[#2B2F36] md:text-6xl">
              {slide.title}
            </h1>
            <p className="mb-10 max-w-lg text-lg font-medium leading-relaxed text-[#64748B]">
              {slide.subtitle}
            </p>
            <div className="flex flex-wrap gap-4">
              <Link
                href="/products"
                className="inline-flex h-14 items-center gap-3 rounded-2xl bg-[#FF7A00] px-8 text-sm font-black text-white transition hover:bg-[#E66E00] shadow-lg shadow-orange-500/20"
              >
                <span className="flex h-6 w-6 items-center justify-center rounded-lg bg-white/20">%</span>
                Kampanyaları Gör
              </Link>
              <Link
                href="/products"
                className="inline-flex h-14 items-center rounded-2xl border-2 border-[#E4E7EB] bg-white px-8 text-sm font-black text-[#2B2F36] transition hover:bg-gray-50"
              >
                Tüm Ürünler →
              </Link>
            </div>

            {/* Pagination */}
            <div className="mt-12 flex gap-3">
              {slides.map((_, idx) => (
                <button
                  key={idx}
                  type="button"
                  onClick={() => setCurrentSlide(idx)}
                  aria-label={`Slayt ${idx + 1}`}
                  className={`h-2 rounded-full transition-all duration-300 ${
                    idx === currentSlide ? "w-10 bg-[#FF7A00]" : "w-2 bg-[#E4E7EB]"
                  }`}
                />
              ))}
            </div>
          </div>

          {/* Right Image */}
          <div className="relative hidden md:block">
            <div className="absolute inset-0 bg-gradient-to-r from-[#FFF8F0] to-transparent z-10" />
            <img
              src={slide.image}
              alt="Gross Market"
              className="h-full w-full object-cover"
            />
            {/* Price Badge */}
            <div className="absolute bottom-12 right-12 z-20 rounded-3xl bg-white p-8 shadow-2xl">
              <p className="text-center text-lg font-black leading-tight text-[#2B2F36] whitespace-pre-line">
                {slide.priceTag}
              </p>
            </div>
          </div>
        </div>

        {/* Navigation */}
        <button
          type="button"
          onClick={handlePrev}
          aria-label="Önceki slayt"
          className="absolute left-6 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white shadow-lg transition hover:bg-gray-50 md:left-12"
        >
          <ChevronLeft size={24} className="text-[#2B2F36]" />
        </button>
        <button
          type="button"
          onClick={handleNext}
          aria-label="Sonraki slayt"
          className="absolute right-6 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white shadow-lg transition hover:bg-gray-50 md:right-12"
        >
          <ChevronRight size={24} className="text-[#2B2F36]" />
        </button>
      </div>
    </section>
  );
}
