import type { Metadata } from "next";
import Link from "next/link";
import { SeoHead } from "@/app/_components/SeoHead";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { storeCampaigns } from "@/lib/content";
import { buildMetadata } from "@/lib/seo";

export const metadata: Metadata = buildMetadata({
  title: "Kampanyalar",
  description: "Karacabey Gross Market kampanya, kupon ve fırsat sayfası.",
  path: "/kampanyalar",
  keywords: ["kampanyalar", "kupon", "indirim", "market fırsatları"],
});

export default function CampaignsPage() {
  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    name: "Karacabey Gross Market Kampanyalar",
    url: "https://karacabeygrossmarket.com/kampanyalar",
  };

  return (
    <GuestLayout>
      <SeoHead data={jsonLd} />
      <main className="catalog-page">
        <section className="catalog-hero">
          <div>
            <p className="eyebrow">Kampanyalar</p>
            <h1>Kupon ve fırsatlar</h1>
          </div>
          <Link className="secondary-action" href="/products">
            Ürünlere Git
          </Link>
        </section>

        <div className="campaign-list">
          {storeCampaigns.map((campaign) => (
            <article className="campaign-card" key={campaign.slug}>
              <p className="eyebrow">Fırsat</p>
              <h2>{campaign.title}</h2>
              <p>{campaign.description}</p>
              <strong>{campaign.discount}</strong>
            </article>
          ))}
        </div>
      </main>
    </GuestLayout>
  );
}
