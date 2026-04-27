import type { Metadata } from "next";

export const siteName = "Karacabey Gross Market";
export const siteUrl = "https://karacabeygrossmarket.com";
export const defaultSeoImage = "/assets/kgm-logo.png";
export const defaultSeoKeywords = [
  "Karacabey Gross Market",
  "Karacabey online market",
  "Bursa market siparişi",
  "gross market",
  "hızlı teslimat",
  "güvenli ödeme",
];

type BuildMetadataInput = {
  title: string;
  description: string;
  path?: string;
  image?: string;
  keywords?: string[];
  robots?: Metadata["robots"];
  type?: "website" | "article";
};

export function buildMetadata({
  title,
  description,
  path = "/",
  image = defaultSeoImage,
  keywords = [],
  robots,
  type = "website",
}: BuildMetadataInput): Metadata {
  const fullTitle = title.includes(siteName) ? title : `${title} | ${siteName}`;
  const canonicalPath = path.startsWith("/") ? path : `/${path}`;
  const url = canonicalPath === "/" ? siteUrl : `${siteUrl}${canonicalPath}`;

  return {
    title: {
      absolute: fullTitle,
    },
    description,
    authors: [{ name: siteName, url: siteUrl }],
    creator: siteName,
    publisher: siteName,
    category: type === "article" ? "article" : "shopping",
    keywords: [...defaultSeoKeywords, ...keywords],
    alternates: {
      canonical: canonicalPath,
    },
    openGraph: {
      title: fullTitle,
      description,
      url,
      siteName,
      locale: "tr_TR",
      type,
      images: [
        {
          url: image,
          alt: fullTitle,
        },
      ],
    },
    twitter: {
      card: "summary_large_image",
      title: fullTitle,
      description,
      images: [image],
    },
    robots,
  };
}
