import type { Metadata } from "next";
import { CheckoutExperience } from "@/app/_components/CheckoutExperience";
import { AppLayout } from "@/app/_layouts/AppLayout";

export const metadata: Metadata = {
  title: "Checkout",
  robots: {
    index: false,
    follow: false,
  },
};

export default function CheckoutPage() {
  return (
    <AppLayout>
      <CheckoutExperience />
    </AppLayout>
  );
}
