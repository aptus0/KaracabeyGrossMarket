import type { Metadata } from "next";
import { Roboto } from "next/font/google";
import { MarketingPixels } from "@/app/_components/MarketingPixels";
import { CartNotification } from "@/app/_components/CartNotification";
import { CampaignModal } from "@/app/_components/CampaignModal";
import { Providers } from "@/app/providers";
import { buildMetadata, siteUrl } from "@/lib/seo";
import "./globals.css";

const roboto = Roboto({
  subsets: ["latin"],
  weight: ["300", "400", "500", "700", "900"],
  display: "swap",
  variable: "--font-sans-roboto",
});

export const metadata: Metadata = {
  metadataBase: new URL(siteUrl),
  ...buildMetadata({
    title: "Karacabey Gross Market",
    description: "Karacabey Gross Market online market, hızlı teslimat, güvenli ödeme ve yerel ürün alışverişi.",
    path: "/",
    keywords: ["Karacabey market", "yerel ürün alışverişi", "online market deneyimi"],
  }),
  applicationName: "Karacabey Gross Market",
  icons: {
    icon: "/favicon.ico",
    shortcut: "/favicon.ico",
    apple: "/assets/kgm-favicon-256.png",
  },
  verification: {
    google: process.env.GOOGLE_SITE_VERIFICATION,
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="tr">
      <body className={`s0 ${roboto.variable}`}>
        <Providers>
          {children}
          <CartNotification />
          <CampaignModal />
          <MarketingPixels />
        </Providers>
      </body>
    </html>
  );
}
