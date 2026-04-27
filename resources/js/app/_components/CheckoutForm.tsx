"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { useState } from "react";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { Button } from "@/app/_components/ui/button";

type CheckoutFormItem = {
  productId: number;
  quantity: number;
};

type CheckoutResponse = {
  message?: string;
  data?: {
    checkout_url?: string;
  };
};

type CheckoutFormProps = {
  items: CheckoutFormItem[];
};

const checkoutEndpoint = `${process.env.NEXT_PUBLIC_API_URL ?? ""}/api/v1/c`;
const checkoutSchema = z.object({
  customer: z.object({
    name: z.string().trim().min(2, "Ad soyad en az 2 karakter olmalı.").max(60, "Ad soyad en fazla 60 karakter olmalı."),
    email: z.string().trim().email("Geçerli bir e-posta gir.").max(100, "E-posta en fazla 100 karakter olmalı."),
    phone: z.string().trim().min(5, "Telefon en az 5 karakter olmalı.").max(20, "Telefon en fazla 20 karakter olmalı."),
  }),
  shipping: z.object({
    address: z.string().trim().min(5, "Teslimat adresi en az 5 karakter olmalı.").max(400, "Teslimat adresi en fazla 400 karakter olmalı."),
  }),
});

type CheckoutFormValues = z.infer<typeof checkoutSchema>;

export function CheckoutForm({ items }: CheckoutFormProps) {
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
      const response = await fetch(checkoutEndpoint, {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          ...values,
          items: items.map((item) => ({
            product_id: item.productId,
            quantity: item.quantity,
          })),
        }),
      });
      const payload = (await response.json().catch(() => null)) as CheckoutResponse | null;

      if (!response.ok) {
        throw new Error(payload?.message ?? "Ödeme başlatılamadı.");
      }

      const checkoutUrl = payload?.data?.checkout_url;

      if (!checkoutUrl) {
        throw new Error("Ödeme bağlantısı oluşturulamadı.");
      }

      window.location.assign(checkoutUrl);
    } catch (caughtError) {
      setError(caughtError instanceof Error ? caughtError.message : "Ödeme başlatılamadı.");
    }
  }

  return (
    <form className="form-stack" onSubmit={handleSubmit(submitCheckout)}>
      <label>
        Ad Soyad
        <input autoComplete="name" {...register("customer.name")} />
        {errors.customer?.name ? <span className="field-error">{errors.customer.name.message}</span> : null}
      </label>
      <label>
        E-posta
        <input type="email" autoComplete="email" {...register("customer.email")} />
        {errors.customer?.email ? <span className="field-error">{errors.customer.email.message}</span> : null}
      </label>
      <label>
        Telefon
        <input autoComplete="tel" {...register("customer.phone")} />
        {errors.customer?.phone ? <span className="field-error">{errors.customer.phone.message}</span> : null}
      </label>
      <label>
        Teslimat Adresi
        <textarea autoComplete="street-address" {...register("shipping.address")} />
        {errors.shipping?.address ? <span className="field-error">{errors.shipping.address.message}</span> : null}
      </label>
      {error ? <p className="form-alert">{error}</p> : null}
      <Button className="primary-action" type="submit" disabled={isSubmitting}>
        {isSubmitting ? "Yönlendiriliyor" : "Ödemeye Devam Et"}
      </Button>
    </form>
  );
}
