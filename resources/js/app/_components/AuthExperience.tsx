"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { LockKeyhole, MapPin, ShieldCheck, Truck } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import type { ReactNode } from "react";
import { useEffect, useMemo, useState } from "react";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { KgmLogo } from "@/app/_components/KgmLogo";
import { Button } from "@/app/_components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/app/_components/ui/card";
import { Input } from "@/app/_components/ui/input";
import { Label } from "@/app/_components/ui/label";
import { Separator } from "@/app/_components/ui/separator";
import { apiRequest, extractErrorMessage } from "@/lib/api";
import { useAuthStore, type AuthUser } from "@/lib/auth-store";
import { useCartStore } from "@/lib/cart-store";

const providerFallback = {
  google: {
    enabled: false,
    label: "Google",
    redirect_url: null,
  },
  facebook: {
    enabled: false,
    label: "Facebook",
    redirect_url: null,
  },
};

const loginSchema = z.object({
  email: z.string().trim().email("Geçerli bir e-posta girin."),
  password: z.string().min(6, "Şifreniz en az 6 karakter olmalı."),
});

const registerSchema = loginSchema.extend({
  name: z.string().trim().min(2, "Ad soyad en az 2 karakter olmalı."),
});

type AuthMode = "login" | "register";
type ProviderMap = typeof providerFallback;
type AuthFormValues = {
  name?: string;
  email: string;
  password: string;
};

type AuthResponse = {
  user: AuthUser;
  token: string;
};

type AuthExperienceProps = {
  mode: AuthMode;
};

export function AuthExperience({ mode }: AuthExperienceProps) {
  const router = useRouter();
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
  const setSession = useAuthStore((state) => state.setSession);
  const initializeCart = useCartStore((state) => state.initialize);
  const cartToken = useCartStore((state) => state.cart_token);
  const [providers, setProviders] = useState<ProviderMap>(providerFallback);
  const [providersLoading, setProvidersLoading] = useState(true);
  const [formError, setFormError] = useState<string | null>(null);

  const schema = useMemo(() => (mode === "login" ? loginSchema : registerSchema), [mode]);

  const {
    formState: { errors, isSubmitting },
    handleSubmit,
    register,
  } = useForm<AuthFormValues>({
    resolver: zodResolver(schema),
    defaultValues: {
      name: "",
      email: "",
      password: "",
    },
  });

  useEffect(() => {
    if (isAuthenticated) {
      router.replace("/account");
    }
  }, [isAuthenticated, router]);

  useEffect(() => {
    let active = true;

    apiRequest<ProviderMap>("/api/v1/auth/providers")
      .then((response) => {
        if (active) {
          setProviders({
            google: response.google ?? providerFallback.google,
            facebook: response.facebook ?? providerFallback.facebook,
          });
        }
      })
      .catch(() => {
        if (active) {
          setProviders(providerFallback);
        }
      })
      .finally(() => {
        if (active) {
          setProvidersLoading(false);
        }
      });

    return () => {
      active = false;
    };
  }, []);

  async function submit(values: AuthFormValues) {
    setFormError(null);

    try {
      const payload = await apiRequest<AuthResponse>(
        mode === "login" ? "/api/v1/auth/login" : "/api/v1/auth/register",
        {
          method: "POST",
          body: JSON.stringify({
            ...values,
            device_name: "next-storefront",
            cart_token: cartToken ?? undefined,
          }),
        },
      );

      setSession(payload.token, payload.user);
      await initializeCart({ silent: true });
      router.replace("/account");
    } catch (error) {
      setFormError(
        extractErrorMessage(
          error,
          mode === "login" ? "Giriş yapılırken bir sorun oluştu." : "Kayıt tamamlanamadı.",
        ),
      );
    }
  }

  function startProvider(provider: keyof ProviderMap) {
    const selectedProvider = providers[provider];

    if (!selectedProvider?.enabled || !selectedProvider.redirect_url) {
      return;
    }

    window.location.assign(selectedProvider.redirect_url);
  }

  return (
    <section className="grid w-full gap-6 lg:grid-cols-[minmax(0,0.94fr)_minmax(0,1.06fr)]">
      <Card className="overflow-hidden border-none bg-[linear-gradient(160deg,#FFF8F0_0%,#FFF1E1_45%,#FFFFFF_100%)] shadow-none">
        <CardContent className="flex h-full flex-col justify-between gap-8 p-8 sm:p-10">
          <div className="space-y-5">
            <div className="inline-flex rounded-2xl border border-white/70 bg-white/85 p-3 shadow-sm">
              <KgmLogo />
            </div>
            <div className="space-y-3">
              <p className="text-xs font-black uppercase tracking-[0.16em] text-[#FF7A00]">
                Karacabey Gross hesabı
              </p>
              <h1 className="text-3xl font-black leading-tight text-[#2B2F36] sm:text-4xl">
                {mode === "login"
                  ? "Siparişlerinize, favorilerinize ve kayıtlı adreslerinize tek yerden ulaşın."
                  : "Daha hızlı checkout ve kayıtlı teslimat bilgileri için yeni hesabınızı oluşturun."}
              </h1>
              <p className="max-w-xl text-sm leading-7 text-[#5F6670] sm:text-base">
                Güvenli ödeme, hızlı teslimat ve profesyonel müşteri akışını aynı hesap deneyiminde topladık.
              </p>
            </div>
          </div>

          <div className="grid gap-3">
            <IntroItem
              icon={<ShieldCheck size={18} />}
              title="Güvenli oturum"
              description="Hesap oturumunuz API token tabanlı olarak korunur ve gerektiğinde tek tıkla sonlandırılır."
            />
            <IntroItem
              icon={<Truck size={18} />}
              title="Daha hızlı teslimat"
              description="Kayıtlı teslimat bilgileri ve sipariş geçmişiyle tekrar sipariş akışı çok daha kısa sürer."
            />
            <IntroItem
              icon={<MapPin size={18} />}
              title="Adres yönetimi"
              description="Ev, iş ve operasyon noktalarınızı tek hesapta düzenleyip checkout sırasında hızla seçebilirsiniz."
            />
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader className="space-y-3">
          <div className="inline-flex w-fit rounded-full border border-[#FFE1C2] bg-[#FFF5EA] px-3 py-1 text-xs font-black uppercase tracking-[0.16em] text-[#FF7A00]">
            {mode === "login" ? "Hesaba giriş" : "Yeni üyelik"}
          </div>
          <CardTitle>{mode === "login" ? "Giriş yapın" : "Hesabınızı oluşturun"}</CardTitle>
          <CardDescription>
            {mode === "login"
              ? "Mevcut hesabınızla devam edin veya sosyal giriş hazır olduğunda tek dokunuşla bağlanın."
              : "Kurumsal ve bireysel teslimatlarınız için modern hesap deneyimine hemen geçin."}
          </CardDescription>
        </CardHeader>

        <CardContent className="space-y-6">
          <div className="grid gap-3 sm:grid-cols-2">
            <ProviderButton
              busy={providersLoading}
              disabled={!providers.google.enabled}
              icon={<GoogleMark />}
              label={providers.google.label}
              onClick={() => startProvider("google")}
            />
            <ProviderButton
              busy={providersLoading}
              disabled={!providers.facebook.enabled}
              icon={<FacebookMark />}
              label={providers.facebook.label}
              onClick={() => startProvider("facebook")}
            />
          </div>

          <div className="flex items-center gap-4">
            <Separator />
            <span className="shrink-0 text-xs font-bold uppercase tracking-[0.14em] text-[#9098A1]">
              veya e-posta ile
            </span>
            <Separator />
          </div>

          <form className="grid gap-4" onSubmit={handleSubmit(submit)}>
            {mode === "register" ? (
              <div className="grid gap-2">
                <Label htmlFor="name">Ad Soyad</Label>
                <Input id="name" autoComplete="name" {...register("name")} />
                {"name" in errors && errors.name ? (
                  <p className="text-sm font-semibold text-[#A32A18]">{errors.name.message}</p>
                ) : null}
              </div>
            ) : null}

            <div className="grid gap-2">
              <Label htmlFor="email">E-posta</Label>
              <Input id="email" type="email" autoComplete="email" {...register("email")} />
              {errors.email ? <p className="text-sm font-semibold text-[#A32A18]">{errors.email.message}</p> : null}
            </div>

            <div className="grid gap-2">
              <Label htmlFor="password">Şifre</Label>
              <Input
                id="password"
                type="password"
                autoComplete={mode === "login" ? "current-password" : "new-password"}
                {...register("password")}
              />
              {errors.password ? (
                <p className="text-sm font-semibold text-[#A32A18]">{errors.password.message}</p>
              ) : null}
            </div>

            {formError ? (
              <div className="rounded-2xl border border-[#F6C7C0] bg-[#FFF3F1] px-4 py-3 text-sm font-semibold text-[#A32A18]">
                {formError}
              </div>
            ) : null}

            <Button className="h-12 rounded-xl text-sm" type="submit" disabled={isSubmitting}>
              <LockKeyhole size={17} />
              {isSubmitting
                ? "İşleniyor"
                : mode === "login"
                  ? "Giriş Yap"
                  : "Kayıt Ol"}
            </Button>
          </form>

          <div className="grid gap-3 rounded-2xl border border-dashed border-[#E4E7EB] bg-[#FAFBFC] p-4 text-sm text-[#5F6670]">
            <p className="font-semibold text-[#2B2F36]">
              {mode === "login"
                ? "Henüz hesabınız yok mu?"
                : "Zaten bir hesabınız var mı?"}
            </p>
            <div className="flex flex-wrap gap-3">
              <Link
                href={mode === "login" ? "/auth/register" : "/auth/login"}
                className="font-black text-[#FF7A00]"
              >
                {mode === "login" ? "Kayıt ekranına geç" : "Giriş ekranına dön"}
              </Link>
              <Link href="/checkout" className="font-bold text-[#5F6670]">
                Checkout’a git
              </Link>
            </div>
          </div>
        </CardContent>
      </Card>
    </section>
  );
}

function IntroItem({
  icon,
  title,
  description,
}: {
  icon: ReactNode;
  title: string;
  description: string;
}) {
  return (
    <div className="grid gap-2 rounded-2xl border border-white/70 bg-white/88 p-4 shadow-sm">
      <div className="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[#FFF0E0] text-[#FF7A00]">
        {icon}
      </div>
      <strong className="text-sm font-black text-[#2B2F36]">{title}</strong>
      <p className="text-sm leading-6 text-[#5F6670]">{description}</p>
    </div>
  );
}

function ProviderButton({
  busy,
  disabled,
  icon,
  label,
  onClick,
}: {
  busy: boolean;
  disabled: boolean;
  icon: ReactNode;
  label: string;
  onClick: () => void;
}) {
  return (
    <Button
      type="button"
      variant="secondary"
      className="h-12 justify-between rounded-xl border-dashed px-4"
      onClick={onClick}
      disabled={busy || disabled}
    >
      <span className="inline-flex items-center gap-2">
        {icon}
        <span>{label}</span>
      </span>
      <span className="text-xs font-black uppercase tracking-[0.12em] text-[#9098A1]">
        {busy ? "yükleniyor" : disabled ? "yakında" : "hazır"}
      </span>
    </Button>
  );
}

function GoogleMark() {
  return (
    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none">
      <path
        d="M21.81 12.24c0-.71-.06-1.39-.18-2.04H12v3.86h5.5a4.7 4.7 0 0 1-2.04 3.08v2.56h3.31c1.94-1.78 3.04-4.41 3.04-7.46Z"
        fill="#4285F4"
      />
      <path
        d="M12 22c2.76 0 5.07-.91 6.76-2.46l-3.31-2.56c-.92.62-2.09 1-3.45 1-2.65 0-4.9-1.79-5.71-4.19H2.87v2.64A9.99 9.99 0 0 0 12 22Z"
        fill="#34A853"
      />
      <path
        d="M6.29 13.79A5.98 5.98 0 0 1 5.97 12c0-.62.11-1.22.32-1.79V7.57H2.87A10 10 0 0 0 2 12c0 1.63.39 3.16 1.07 4.43l3.22-2.64Z"
        fill="#FBBC04"
      />
      <path
        d="M12 6.02c1.5 0 2.84.52 3.9 1.53l2.92-2.92C17.06 2.98 14.75 2 12 2 8.09 2 4.72 4.24 3.07 7.57l3.22 2.64C7.1 7.81 9.35 6.02 12 6.02Z"
        fill="#EA4335"
      />
    </svg>
  );
}

function FacebookMark() {
  return (
    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none">
      <path
        d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07c0 6.03 4.39 11.02 10.12 11.93V15.56H7.08v-3.49h3.04V9.41c0-3.03 1.79-4.7 4.54-4.7 1.31 0 2.68.24 2.68.24v2.97h-1.51c-1.49 0-1.95.93-1.95 1.88v2.27h3.32l-.53 3.49h-2.79V24C19.61 23.09 24 18.1 24 12.07Z"
        fill="#1877F2"
      />
      <path
        d="M16.67 15.56l.53-3.49h-3.32V9.8c0-.95.46-1.88 1.95-1.88h1.51V4.95s-1.37-.24-2.68-.24c-2.75 0-4.54 1.67-4.54 4.7v2.66H7.08v3.49h3.04V24a12.14 12.14 0 0 0 3.76 0v-8.44h2.79Z"
        fill="#FFFFFF"
      />
    </svg>
  );
}
