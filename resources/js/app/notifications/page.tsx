import type { Metadata } from "next";
import { NotificationsCenter } from "@/app/_components/NotificationsCenter";
import { GuestLayout } from "@/app/_layouts/GuestLayout";
import { buildMetadata } from "@/lib/seo";

export const metadata: Metadata = buildMetadata({
  title: "Bildirimler",
  description: "Yeni kampanyalar, ürün güncellemeleri ve sipariş haberleri için bildirim merkezi.",
  path: "/notifications",
  keywords: ["bildirim", "kampanya bildirimi", "urun bildirimi", "hesap bildirimi"],
});

export default function NotificationsPage() {
  return (
    <GuestLayout>
      <main className="content-page">
        <NotificationsCenter />
      </main>
    </GuestLayout>
  );
}
