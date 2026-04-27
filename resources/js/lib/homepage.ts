export type HomepageBlock = {
  id: number;
  type: string;
  title: string;
  subtitle?: string | null;
  image_url?: string | null;
  link_url?: string | null;
  link_label?: string | null;
};

type HomepageResponse = {
  data?: {
    blocks?: HomepageBlock[];
  };
};

export const fallbackSlides: HomepageBlock[] = [
  {
    id: 1,
    type: "carousel_slide",
    title: "Karacabey Gross Market",
    subtitle: "Toptan fiyatına, güvenle alışveriş. Yerel ürünler ve hızlı teslimat tek ekranda.",
    image_url: "/assets/kgm-logo-4k.png",
    link_url: "/products",
    link_label: "Alışverişe Başla",
  },
  {
    id: 2,
    type: "carousel_slide",
    title: "Haftalık Gross Fırsatları",
    subtitle: "Temel gıda, kahvaltılık ve günlük ihtiyaçlarda avantajlı sepet ürünleri.",
    image_url: "https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1600&q=85",
    link_url: "/kampanyalar",
    link_label: "Kampanyaları Gör",
  },
];

const apiBaseUrl = process.env.NEXT_PUBLIC_API_URL ?? "";

export async function fetchHomepageBlocks(signal?: AbortSignal): Promise<HomepageBlock[]> {
  const response = await fetch(`${apiBaseUrl}/api/v1/content/homepage`, {
    headers: {
      Accept: "application/json",
    },
    signal,
  });

  if (!response.ok) {
    throw new Error("Homepage content failed.");
  }

  const payload = (await response.json()) as HomepageResponse;

  return payload.data?.blocks ?? [];
}
