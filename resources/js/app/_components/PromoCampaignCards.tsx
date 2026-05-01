"use client";

import React from "react";
import Link from "next/link";
import { ChevronRight } from "lucide-react";

interface PromoCard {
  title: string;
  subtitle?: string;
  description: string;
  image: string;
  href: string;
  buttonText: string;
  bgColor: string;
  titleColor: string;
}

const campaignCards: PromoCard[] = [
  {
    title: "Meyve & Sebzede\n%20'ye Varan\nİndirimleri",
    subtitle: "Taze ve vitamin\ndolu meyvlerde fırsat!",
    description: "Tazelik kaçırmayın!",
    image:
      "https://images.unsplash.com/photo-1488459716781-6815cecdf030?w=400&h=400&fit=crop",
    href: "/products?category=meyve-sebze",
    buttonText: "Alışverişe Başla",
    bgColor: "bg-green-50",
    titleColor: "text-green-700",
  },
  {
    title: "Haftanın\nSepet\nFırsatları",
    subtitle: "Avantajlı paket ürünler\niçin takayın.",
    description: "Fırsatları İncele",
    image:
      "https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&h=400&fit=crop",
    href: "/products?q=fırsat",
    buttonText: "Fırsatları İncele",
    bgColor: "bg-orange-50",
    titleColor: "text-orange-700",
  },
  {
    title: "Toplu\nAlışverişe\nEkstra İndirim",
    subtitle: "İş yerleri ve toplu alımlar\niçin özel fiyatlar!",
    description: "Teklif Al",
    image:
      "https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=400&h=400&fit=crop",
    href: "/products?q=toplu",
    buttonText: "Teklif Al",
    bgColor: "bg-blue-900",
    titleColor: "text-white",
  },
];

export function PromoCampaignCards() {
  return (
    <section className="max-w-[1120px] mx-auto px-4 md:px-6 py-8">
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {campaignCards.map((card) => (
          <Link
            key={card.title}
            href={card.href}
            className={`${card.bgColor} rounded-3xl overflow-hidden transition-transform hover:scale-105 duration-300 group cursor-pointer`}
          >
            <div className="grid grid-cols-2 gap-4 items-center min-h-[300px] p-6 md:p-8">
              {/* Content Side */}
              <div className="flex flex-col justify-between h-full">
                <div>
                  <h3
                    className={`text-2xl md:text-3xl font-bold leading-tight mb-3 whitespace-pre-line ${card.titleColor}`}
                  >
                    {card.title}
                  </h3>
                  {card.subtitle && (
                    <p className="text-sm text-gray-700 whitespace-pre-line mb-4">
                      {card.subtitle}
                    </p>
                  )}
                </div>

                {/* Button */}
                <button
                  type="button"
                  className={`inline-flex items-center gap-2 px-6 py-2 rounded-full font-semibold transition-colors w-fit ${
                    card.bgColor === "bg-blue-900"
                      ? "bg-white text-blue-900 hover:bg-gray-100"
                      : "bg-white text-gray-900 hover:bg-gray-100"
                  }`}
                >
                  {card.buttonText}
                  <ChevronRight size={18} />
                </button>
              </div>

              {/* Image Side */}
              <div className="relative h-full min-h-[250px] rounded-2xl overflow-hidden">
                <img
                  src={card.image}
                  alt={card.title}
                  className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                />
              </div>
            </div>
          </Link>
        ))}
      </div>
    </section>
  );
}
