import type { Metadata } from "next";
import { CheckoutExperience } from "@/app/_components/CheckoutExperience";
import { AppLayout } from "@/app/_layouts/AppLayout";
import { buildMetadata } from "@/lib/seo";

export const metadata: Metadata = buildMetadata({
  title: "Checkout",
  description: "Karacabey Gross Market checkout ve ödeme adımı.",
  path: "/checkout",
  keywords: ["checkout", "ödeme", "satın al"],
  robots: {
    index: false,
    follow: false,
  },
});

export default function CheckoutPage() {
  return (
    <AppLayout>
      <CheckoutExperience />
    </AppLayout>
  );
}
