import type { Metadata } from "next";
import { MarketingPixels } from "@/app/_components/MarketingPixels";
import { Providers } from "@/app/providers";
import "./globals.css";

export const metadata: Metadata = {
  metadataBase: new URL("https://karacabeygrossmarket.com"),
  title: {
    default: "Karacabey Gross Market",
    template: "%s | Karacabey Gross Market",
  },
  description:
    "Karacabey Gross Market online market, hızlı teslimat, güvenli ödeme ve yerel ürün alışverişi.",
  applicationName: "Karacabey Gross Market",
  alternates: {
    canonical: "/",
  },
  icons: {
    icon: "/favicon.ico",
    shortcut: "/favicon.ico",
    apple: "/assets/kgm-favicon-256.png",
  },
  openGraph: {
    title: "Karacabey Gross Market",
    description:
      "Karacabey için profesyonel online gross market deneyimi.",
    url: "https://karacabeygrossmarket.com",
    siteName: "Karacabey Gross Market",
    locale: "tr_TR",
    type: "website",
    images: ["/assets/kgm-logo.png"],
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
        <Providers>
          {children}
          <MarketingPixels />
        </Providers>
      </body>
    </html>
  );
}
