import type { Metadata } from "next";
import { AuthExperience } from "@/app/_components/AuthExperience";
import { AuthLayout } from "@/app/_layouts/AuthLayout";

export const metadata: Metadata = {
  title: "Kayıt",
  robots: {
    index: false,
    follow: false,
  },
};

export default function RegisterPage() {
  return (
    <AuthLayout>
      <AuthExperience mode="register" />
    </AuthLayout>
  );
}
