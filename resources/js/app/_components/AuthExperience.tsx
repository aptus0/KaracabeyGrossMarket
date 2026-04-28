"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { CheckCircle2, Eye, EyeOff, LockKeyhole, Phone, Timer, User } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { KgmLogo } from "@/app/_components/KgmLogo";
import { apiRequest, extractErrorMessage } from "@/lib/api";
import { useAuthStore, type AuthUser } from "@/lib/auth-store";
import { useCartStore } from "@/lib/cart-store";

// ─── Schemas ─────────────────────────────────────────────────────────────────

const phoneSchema = z
  .string()
  .trim()
  .min(10, "Geçerli bir telefon numarası girin.")
  .max(20)
  .regex(/^[0-9+\s\-()]+$/, "Yalnızca rakam giriniz.");

const loginSchema = z.object({
  phone: phoneSchema,
  password: z.string().min(1, "Şifrenizi girin."),
});

const passwordStrengthSchema = z
  .string()
  .min(8, "En az 8 karakter olmalı.")
  .regex(/[A-Z]/, "En az 1 büyük harf içermeli.")
  .regex(/[a-z]/, "En az 1 küçük harf içermeli.")
  .regex(/[0-9]/, "En az 1 rakam içermeli.");

const registerSchema = z.object({
  name: z.string().trim().min(2, "Ad soyad en az 2 karakter olmalı.").max(120),
  phone: phoneSchema,
  password: passwordStrengthSchema,
});

// ─── Geo detection ───────────────────────────────────────────────────────────

async function detectLocation(): Promise<string | null> {
  try {
    const res = await fetch("https://ipapi.co/json/", { signal: AbortSignal.timeout(4000) });
    if (!res.ok) return null;
    const data = (await res.json()) as { city?: string; region?: string; country_name?: string };
    const parts = [data.city, data.region, data.country_name].filter(Boolean);
    return parts.length ? parts.join(", ") : null;
  } catch {
    return null;
  }
}

// ─── Password strength ───────────────────────────────────────────────────────

type StrengthLevel = 0 | 1 | 2 | 3 | 4;

function measureStrength(pw: string): StrengthLevel {
  if (!pw) return 0;
  let score = 0;
  if (pw.length >= 8) score++;
  if (/[A-Z]/.test(pw)) score++;
  if (/[0-9]/.test(pw)) score++;
  if (/[^A-Za-z0-9]/.test(pw)) score++;
  return Math.min(4, score) as StrengthLevel;
}

const strengthConfig: Record<StrengthLevel, { label: string; color: string; pct: string }> = {
  0: { label: "",          color: "#e4e7eb", pct: "0%" },
  1: { label: "Çok Zayıf", color: "#ef4444", pct: "25%" },
  2: { label: "Zayıf",     color: "#f97316", pct: "50%" },
  3: { label: "İyi",       color: "#eab308", pct: "75%" },
  4: { label: "Güçlü",     color: "#22c55e", pct: "100%" },
};

// ─── Types ────────────────────────────────────────────────────────────────────

type AuthMode = "login" | "register";
type AuthFormValues = { name?: string; phone: string; password: string };
type AuthResponse = { user: AuthUser; token: string };

// ─── Component ───────────────────────────────────────────────────────────────

export function AuthExperience({ mode }: { mode: AuthMode }) {
  const router         = useRouter();
  const isAuthenticated = useAuthStore((s) => s.isAuthenticated);
  const setSession      = useAuthStore((s) => s.setSession);
  const initializeCart  = useCartStore((s) => s.initialize);
  const cartToken       = useCartStore((s) => s.cart_token);

  const [showPassword, setShowPassword]       = useState(false);
  const [formError, setFormError]             = useState<string | null>(null);
  const [successState, setSuccessState]       = useState(false);
  const [countdown, setCountdown]             = useState(0);
  const [locked, setLocked]                   = useState(false);
  const [remaining, setRemaining]             = useState<number | null>(null);
  const countdownRef = useRef<ReturnType<typeof setInterval> | null>(null);

  const schema = useMemo(() => (mode === "login" ? loginSchema : registerSchema), [mode]);

  const { formState: { errors, isSubmitting }, handleSubmit, register, watch } =
    useForm<AuthFormValues>({
      resolver: zodResolver(schema),
      defaultValues: { name: "", phone: "", password: "" },
    });

  const passwordValue = watch("password") ?? "";
  const strength      = measureStrength(passwordValue);
  const strengthCfg   = strengthConfig[strength];

  useEffect(() => {
    if (isAuthenticated) router.replace("/account");
  }, [isAuthenticated, router]);

  useEffect(() => () => { if (countdownRef.current) clearInterval(countdownRef.current); }, []);

  const startCountdown = useCallback((seconds: number) => {
    setCountdown(seconds);
    if (countdownRef.current) clearInterval(countdownRef.current);
    countdownRef.current = setInterval(() => {
      setCountdown((prev) => {
        if (prev <= 1) {
          clearInterval(countdownRef.current!);
          setLocked(false);
          setRemaining(null);
          setFormError(null);
          return 0;
        }
        return prev - 1;
      });
    }, 1000);
  }, []);

  async function submit(values: AuthFormValues) {
    setFormError(null);

    const location = await detectLocation();

    try {
      const payload = await apiRequest<AuthResponse>(
        mode === "login" ? "/api/v1/auth/login" : "/api/v1/auth/register",
        {
          method: "POST",
          body: JSON.stringify({
            ...values,
            location,
            device_name: "next-storefront",
            cart_token: cartToken ?? undefined,
          }),
        },
      );

      setSuccessState(true);
      setTimeout(async () => {
        setSession(payload.token, payload.user);
        await initializeCart({ silent: true });
        router.replace("/account");
      }, 600);
    } catch (error: unknown) {
      const apiError = error as { remaining_attempts?: number; locked?: boolean; retry_after?: number } | null;

      if (apiError && typeof apiError === "object") {
        const rem     = apiError.remaining_attempts;
        const isLocked = apiError.locked ?? false;
        const retry   = apiError.retry_after;

        setRemaining(typeof rem === "number" ? rem : null);
        setLocked(isLocked);

        if (isLocked && retry) startCountdown(retry);
      }

      setFormError(
        extractErrorMessage(error, mode === "login" ? "Giriş yapılırken bir sorun oluştu." : "Kayıt tamamlanamadı."),
      );
    }
  }

  const showWarning = mode === "login" && remaining !== null && remaining <= 2 && remaining > 0;

  return (
    <div className="auth-card">
      {/* Logo */}
      <div className="auth-card__logo">
        <Link href="/">
          <KgmLogo />
        </Link>
      </div>

      {/* Header */}
      <div className="auth-card__header">
        <span className="auth-card__badge">
          {mode === "login" ? "Hesaba Giriş" : "Yeni Üyelik"}
        </span>
        <h1 className="auth-card__title">
          {mode === "login" ? "Giriş yapın" : "Hesap oluşturun"}
        </h1>
        <p className="auth-card__sub">
          {mode === "login"
            ? "Siparişlerinize ve adreslerinize hızlı erişin."
            : "Dakikalar içinde ücretsiz hesabınızı oluşturun."}
        </p>
      </div>

      {/* Lock alert */}
      {locked && countdown > 0 && (
        <div className="auth-alert auth-alert--danger">
          <Timer size={16} />
          <div>
            <strong>Hesap geçici kilitlendi</strong>
            <span>{Math.floor(countdown / 60)} dk {countdown % 60} sn sonra tekrar deneyin.</span>
          </div>
        </div>
      )}

      {/* Remaining attempts warning */}
      {showWarning && (
        <div className="auth-alert auth-alert--warn">
          <span>Dikkat! Yalnızca <strong>{remaining}</strong> giriş hakkınız kaldı.</span>
        </div>
      )}

      {/* Form */}
      <form className="auth-form" onSubmit={handleSubmit(submit)} noValidate>

        {/* Name — register only */}
        {mode === "register" && (
          <div className="auth-field">
            <label htmlFor="name">Ad Soyad</label>
            <div className="auth-field__input-wrap">
              <User size={15} className="auth-field__icon" />
              <input
                id="name"
                type="text"
                autoComplete="name"
                placeholder="Adınız Soyadınız"
                {...register("name")}
              />
            </div>
            {"name" in errors && errors.name
              ? <span className="auth-field__error">{errors.name.message}</span>
              : null}
          </div>
        )}

        {/* Phone */}
        <div className="auth-field">
          <label htmlFor="phone">Telefon Numarası</label>
          <div className="auth-field__input-wrap">
            <Phone size={15} className="auth-field__icon" />
            <input
              id="phone"
              type="tel"
              autoComplete="tel"
              placeholder="5xx xxx xx xx"
              {...register("phone")}
            />
          </div>
          {errors.phone
            ? <span className="auth-field__error">{errors.phone.message}</span>
            : null}
        </div>

        {/* Password */}
        <div className="auth-field">
          <label htmlFor="password">Şifre</label>
          <div className="auth-field__input-wrap">
            <LockKeyhole size={15} className="auth-field__icon" />
            <input
              id="password"
              type={showPassword ? "text" : "password"}
              autoComplete={mode === "login" ? "current-password" : "new-password"}
              placeholder={mode === "login" ? "Şifreniz" : "En az 8 karakter"}
              {...register("password")}
            />
            <button
              type="button"
              className="auth-field__toggle"
              onClick={() => setShowPassword((p) => !p)}
              aria-label={showPassword ? "Şifreyi gizle" : "Şifreyi göster"}
            >
              {showPassword ? <EyeOff size={15} /> : <Eye size={15} />}
            </button>
          </div>
          {errors.password
            ? <span className="auth-field__error">{errors.password.message}</span>
            : null}

          {/* Strength bar — register only */}
          {mode === "register" && passwordValue && (
            <div className="auth-strength">
              <div className="auth-strength__track">
                <div
                  className="auth-strength__fill"
                  style={{ width: strengthCfg.pct, background: strengthCfg.color }}
                />
              </div>
              {strengthCfg.label && (
                <span className="auth-strength__label" style={{ color: strengthCfg.color }}>
                  {strengthCfg.label}
                </span>
              )}
            </div>
          )}
        </div>

        {/* Password rules — register only */}
        {mode === "register" && (
          <ul className="auth-rules">
            {[
              { ok: passwordValue.length >= 8, text: "En az 8 karakter" },
              { ok: /[A-Z]/.test(passwordValue), text: "En az 1 büyük harf" },
              { ok: /[a-z]/.test(passwordValue), text: "En az 1 küçük harf" },
              { ok: /[0-9]/.test(passwordValue),  text: "En az 1 rakam" },
            ].map(({ ok, text }) => (
              <li key={text} data-ok={ok}>
                <CheckCircle2 size={11} />
                {text}
              </li>
            ))}
          </ul>
        )}

        {/* Form error */}
        {formError && !locked && (
          <div className="auth-alert auth-alert--danger">
            <span>{formError}</span>
          </div>
        )}

        {/* Submit */}
        <button
          type="submit"
          className="auth-submit"
          data-success={successState}
          disabled={isSubmitting || locked || successState}
        >
          {successState
            ? <><CheckCircle2 size={16} /> Başarılı!</>
            : isSubmitting
              ? "İşleniyor…"
              : mode === "login" ? "Giriş Yap" : "Kayıt Ol"}
        </button>
      </form>

      {/* Switch mode */}
      <div className="auth-switch">
        <span>
          {mode === "login" ? "Hesabınız yok mu?" : "Hesabınız var mı?"}
        </span>
        <Link href={mode === "login" ? "/auth/register" : "/auth/login"}>
          {mode === "login" ? "Kayıt ol" : "Giriş yap"}
        </Link>
        <Link href="/" className="auth-switch__home">Ana sayfa</Link>
      </div>
    </div>
  );
}
