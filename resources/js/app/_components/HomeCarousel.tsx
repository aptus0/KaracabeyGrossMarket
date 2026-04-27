"use client";

import Link from "next/link";
import { useQuery } from "@tanstack/react-query";
import { Autoplay, Navigation, Pagination } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/react";
import { fallbackSlides, fetchHomepageBlocks } from "@/lib/homepage";

export function HomeCarousel() {
  const { data } = useQuery({
    queryKey: ["homepage", "carousel"],
    queryFn: ({ signal }) => fetchHomepageBlocks(signal),
  });
  const slides = data?.filter((block) => block.type === "carousel_slide") ?? fallbackSlides;

  return (
    <section className="home-carousel home-carousel--swiper" aria-label="Ana sayfa slider">
      <Swiper
        autoplay={{ delay: 6500, disableOnInteraction: false }}
        className="home-carousel__swiper"
        loop={slides.length > 1}
        modules={[Autoplay, Navigation, Pagination]}
        navigation
        pagination={{ clickable: true }}
      >
        {slides.map((slide) => {
          const safeImage = safeMediaUrl(slide.image_url);

          return (
            <SwiperSlide key={slide.id}>
              <div className="home-carousel__slide">
                <div className="home-carousel__media" aria-hidden="true">
                  {safeImage ? (
                    // eslint-disable-next-line @next/next/no-img-element
                    <img src={safeImage} alt="" />
                  ) : null}
                </div>
                <div className="home-carousel__content">
                  <p className="eyebrow">Karacabey Gross Market</p>
                  <h1>{slide.title}</h1>
                  {slide.subtitle ? <p>{slide.subtitle}</p> : null}
                  <div className="hero-section__actions">
                    {slide.link_url ? (
                      <Link className="primary-action" href={slide.link_url}>
                        {slide.link_label || "İncele"}
                      </Link>
                    ) : null}
                    <Link className="secondary-action" href="/products">
                      Tüm Ürünler
                    </Link>
                  </div>
                </div>
              </div>
            </SwiperSlide>
          );
        })}
      </Swiper>
    </section>
  );
}

function safeMediaUrl(url?: string | null): string | null {
  if (!url) {
    return null;
  }

  if (url.startsWith("/") && !url.startsWith("//")) {
    return url;
  }

  try {
    const parsedUrl = new URL(url);

    return parsedUrl.protocol === "https:" || parsedUrl.protocol === "http:" ? url : null;
  } catch {
    return null;
  }
}
