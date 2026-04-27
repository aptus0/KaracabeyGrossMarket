import type { Metadata } from "next";
import { AuthExperience } from "@/app/_components/AuthExperience";
import { AuthLayout } from "@/app/_layouts/AuthLayout";

export const metadata: Metadata = {
  title: "Giriş",
  robots: {
    index: false,
    follow: false,
  },
};

export default function LoginPage() {
  return (
    <AuthLayout>
      <AuthExperience mode="login" />
    </AuthLayout>
  );
}
