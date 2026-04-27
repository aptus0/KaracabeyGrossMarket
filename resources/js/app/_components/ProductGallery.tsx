"use client";

import { useMemo, useState, type CSSProperties, type PointerEvent } from "react";
import type { Swiper as SwiperType } from "swiper";
import { FreeMode, Navigation, Thumbs } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/react";

type ProductGalleryProps = {
  images: string[];
  name: string;
};

export function ProductGallery({ images, name }: ProductGalleryProps) {
  const [thumbsSwiper, setThumbsSwiper] = useState<SwiperType | null>(null);
  const [zoomPosition, setZoomPosition] = useState({ x: 50, y: 50 });
  const safeImages = useMemo(() => images.filter(Boolean), [images]);

  function handlePointerMove(event: PointerEvent<HTMLDivElement>) {
    const bounds = event.currentTarget.getBoundingClientRect();
    const x = ((event.clientX - bounds.left) / bounds.width) * 100;
    const y = ((event.clientY - bounds.top) / bounds.height) * 100;

    setZoomPosition({
      x: Math.min(100, Math.max(0, x)),
      y: Math.min(100, Math.max(0, y)),
    });
  }

  return (
    <div className="product-gallery">
      <Swiper
        className="product-gallery__main"
        modules={[FreeMode, Navigation, Thumbs]}
        navigation={safeImages.length > 1}
        spaceBetween={12}
        thumbs={{ swiper: thumbsSwiper && !thumbsSwiper.destroyed ? thumbsSwiper : null }}
      >
        {safeImages.map((image, index) => (
          <SwiperSlide key={`${image}-${index}`}>
            <div
              className="product-gallery__image"
              onPointerMove={handlePointerMove}
              style={
                {
                  "--zoom-image": `url("${image}")`,
                  "--zoom-x": `${zoomPosition.x}%`,
                  "--zoom-y": `${zoomPosition.y}%`,
                } as CSSProperties
              }
            >
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src={image} alt={index === 0 ? name : `${name} görsel ${index + 1}`} />
              <span className="product-gallery__zoom" aria-hidden="true" />
            </div>
          </SwiperSlide>
        ))}
      </Swiper>

      {safeImages.length > 1 ? (
        <Swiper
          className="product-gallery__thumbs"
          freeMode
          modules={[FreeMode, Thumbs]}
          onSwiper={setThumbsSwiper}
          slidesPerView="auto"
          spaceBetween={10}
          watchSlidesProgress
        >
          {safeImages.map((image, index) => (
            <SwiperSlide key={`thumb-${image}-${index}`}>
              <button type="button" aria-label={`${name} görsel ${index + 1}`}>
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img src={image} alt="" />
              </button>
            </SwiperSlide>
          ))}
        </Swiper>
      ) : null}
    </div>
  );
}
