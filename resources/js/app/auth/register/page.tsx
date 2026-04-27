import type { Metadata } from "next";
import { AuthExperience } from "@/app/_components/AuthExperience";
import { AuthLayout } from "@/app/_layouts/AuthLayout";
import { buildMetadata } from "@/lib/seo";

export const metadata: Metadata = buildMetadata({
  title: "Kayıt",
  description: "Karacabey Gross Market yeni üyelik oluşturma sayfası.",
  path: "/auth/register",
  keywords: ["kayıt", "üyelik oluştur", "müşteri hesabı"],
  robots: {
    index: false,
    follow: false,
  },
});

export default function RegisterPage() {
  return (
    <AuthLayout>
      <AuthExperience mode="register" />
    </AuthLayout>
  );
}
