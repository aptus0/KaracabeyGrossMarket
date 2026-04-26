import type { Metadata } from "next";
import { MarketingPixels } from "@/app/_components/MarketingPixels";
import "./globals.css";

export const metadata: Metadata = {
  metadataBase: new URL("https://karacabeygrossmarket.com"),
  title: {
    default: "Karacabey Gross Market",
    template: "%s | Karacabey Gross Market",
  },
  description:
    "Karacabey Gross Market online market, hızlı teslimat, güvenli PayTR ödeme ve yerel ürün alışverişi.",
  applicationName: "Karacabey Gross Market",
  alternates: {
    canonical: "/",
  },
  openGraph: {
    title: "Karacabey Gross Market",
    description:
      "Karacabey için profesyonel online gross market deneyimi.",
    url: "https://karacabeygrossmarket.com",
    siteName: "Karacabey Gross Market",
    locale: "tr_TR",
    type: "website",
  },
  robots: {
    index: true,
    follow: true,
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
      <body className="s0">
        {children}
        <MarketingPixels />
      </body>
    </html>
  );
}
