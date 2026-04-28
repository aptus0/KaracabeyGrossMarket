"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { useState } from "react";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { Button } from "@/app/_components/ui/button";
import { Input } from "@/app/_components/ui/input";
import { Label } from "@/app/_components/ui/label";
import { apiRequest, extractErrorMessage } from "@/lib/api";
import { useAuthStore } from "@/lib/auth-store";

type CheckoutFormItem = {
  productId: number;
  quantity: number;
};

type CheckoutResponse = {
  checkout_url?: string;
};

type CheckoutFormProps = {
  items: CheckoutFormItem[];
  cartToken?: string | null;
  couponCode?: string | null;
  disabled?: boolean;
};

const checkoutSchema = z.object({
  customer: z.object({
    name: z.string().trim().min(2, "Ad soyad en az 2 karakter olmalı.").max(60, "Ad soyad en fazla 60 karakter olmalı."),
    email: z.string().trim().email("Geçerli bir e-posta gir.").max(100, "E-posta en fazla 100 karakter olmalı."),
    phone: z.string().trim().min(5, "Telefon en az 5 karakter olmalı.").max(20, "Telefon en fazla 20 karakter olmalı."),
  }),
  shipping: z.object({
    city: z.string().trim().max(120, "Şehir en fazla 120 karakter olmalı.").optional(),
    district: z.string().trim().max(120, "İlçe en fazla 120 karakter olmalı.").optional(),
    address: z.string().trim().min(5, "Teslimat adresi en az 5 karakter olmalı.").max(400, "Teslimat adresi en fazla 400 karakter olmalı."),
  }),
});

type CheckoutFormValues = z.infer<typeof checkoutSchema>;

export function CheckoutForm({ items, cartToken, couponCode, disabled = false }: CheckoutFormProps) {
  const token = useAuthStore((state) => state.token);
  const [error, setError] = useState<string | null>(null);
  const {
    formState: { errors, isSubmitting },
    handleSubmit,
    register,
  } = useForm<CheckoutFormValues>({
    resolver: zodResolver(checkoutSchema),
  });

  async function submitCheckout(values: CheckoutFormValues) {
    setError(null);

    try {
      const payload = await apiRequest<CheckoutResponse>("/api/v1/c", {
        method: "POST",
        headers: {
          ...(token ? { Authorization: `Bearer ${token}` } : {}),
          ...(!token && cartToken ? { "X-Cart-Token": cartToken } : {}),
        },
        body: JSON.stringify({
          ...values,
          cart_token: !token ? cartToken ?? undefined : undefined,
          coupon_code: couponCode ?? undefined,
          items: items.map((item) => ({
            product_id: item.productId,
            quantity: item.quantity,
          })),
        }),
      });
      const checkoutUrl = payload?.checkout_url;

      if (!checkoutUrl) {
        throw new Error("Ödeme bağlantısı oluşturulamadı.");
      }

      window.location.assign(checkoutUrl);
    } catch (caughtError) {
      setError(extractErrorMessage(caughtError, "Ödeme başlatılamadı."));
    }
  }

  return (
    <form className="form-stack" onSubmit={handleSubmit(submitCheckout)}>
      <div className="grid gap-2">
        <Label htmlFor="checkout-name">Ad Soyad</Label>
        <Input id="checkout-name" autoComplete="name" {...register("customer.name")} />
        {errors.customer?.name ? <span className="field-error">{errors.customer.name.message}</span> : null}
      </div>
      <div className="grid gap-2 sm:grid-cols-2">
        <div className="grid gap-2">
          <Label htmlFor="checkout-email">E-posta</Label>
          <Input id="checkout-email" type="email" autoComplete="email" {...register("customer.email")} />
          {errors.customer?.email ? <span className="field-error">{errors.customer.email.message}</span> : null}
        </div>
        <div className="grid gap-2">
          <Label htmlFor="checkout-phone">Telefon</Label>
          <Input id="checkout-phone" autoComplete="tel" {...register("customer.phone")} />
          {errors.customer?.phone ? <span className="field-error">{errors.customer.phone.message}</span> : null}
        </div>
      </div>
      <div className="grid gap-2 sm:grid-cols-2">
        <div className="grid gap-2">
          <Label htmlFor="checkout-city">Şehir</Label>
          <Input id="checkout-city" autoComplete="address-level1" {...register("shipping.city")} />
          {errors.shipping?.city ? <span className="field-error">{errors.shipping.city.message}</span> : null}
        </div>
        <div className="grid gap-2">
          <Label htmlFor="checkout-district">İlçe</Label>
          <Input id="checkout-district" autoComplete="address-level2" {...register("shipping.district")} />
          {errors.shipping?.district ? <span className="field-error">{errors.shipping.district.message}</span> : null}
        </div>
      </div>
      <div className="grid gap-2">
        <Label htmlFor="checkout-address">Teslimat Adresi</Label>
        <textarea id="checkout-address" autoComplete="street-address" {...register("shipping.address")} />
        {errors.shipping?.address ? <span className="field-error">{errors.shipping.address.message}</span> : null}
      </div>
      {error ? <p className="form-alert">{error}</p> : null}
      <Button className="primary-action h-12 rounded-xl" type="submit" disabled={isSubmitting || disabled || items.length === 0}>
        {isSubmitting ? "Yönlendiriliyor" : "Ödemeye Devam Et"}
      </Button>
    </form>
  );
}
