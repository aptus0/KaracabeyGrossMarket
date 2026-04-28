"use client";

import { useEffect, useRef, useState } from "react";
import { CheckCircle2, ChevronDown, Loader2, Tag, X } from "lucide-react";
import { Button } from "@/app/_components/ui/button";
import { Input } from "@/app/_components/ui/input";
import { formatCartMoney, type AppliedCoupon } from "@/lib/cart";
import { cn } from "@/lib/utils";

export type CouponData = AppliedCoupon;

type CouponInputProps = {
  appliedCoupon: CouponData | null;
  onApply: (code: string) => Promise<unknown> | void;
  onRemove: () => Promise<unknown> | void;
  disabled?: boolean;
};

export function CouponInput({
  appliedCoupon,
  onApply,
  onRemove,
  disabled = false,
}: CouponInputProps) {
  const [open, setOpen] = useState(Boolean(appliedCoupon));
  const [code, setCode] = useState(appliedCoupon?.code ?? "");
  const [validating, setValidating] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    setCode(appliedCoupon?.code ?? "");
    setOpen(Boolean(appliedCoupon));
    setError(null);
  }, [appliedCoupon]);

  function handleToggle() {
    setOpen((prev) => {
      const next = !prev;
      if (next) setTimeout(() => inputRef.current?.focus(), 50);
      return next;
    });
    setError(null);
  }

  function handleRemove() {
    setCode("");
    setError(null);
    setOpen(false);
    onRemove();
  }

  async function handleApply() {
    const trimmed = code.trim().toUpperCase();
    if (!trimmed) return;

    setValidating(true);
    setError(null);

    try {
      await onApply(trimmed);
    } catch (err: unknown) {
      setError(err instanceof Error ? err.message : "Kupon uygulanamadı.");
    } finally {
      setValidating(false);
    }
  }

  if (appliedCoupon) {
    return (
      <div className="flex items-center justify-between rounded-2xl border border-[#DCFCE7] bg-[#F0FDF4] px-4 py-3">
        <div className="flex min-w-0 items-center gap-2">
          <CheckCircle2 size={16} className="shrink-0 text-[#16A34A]" />
          <div className="min-w-0">
            <p className="font-mono text-sm font-black tracking-wider text-[#15803D]">
              {appliedCoupon.code}
            </p>
            <p className="text-xs text-[#16A34A]">
              {appliedCoupon.discount_type === "percent"
                ? `%${appliedCoupon.discount_value} indirim`
                : formatCartMoney(appliedCoupon.discount_cents)}{" "}
              uygulandı
            </p>
          </div>
        </div>
        <button
          type="button"
          onClick={handleRemove}
          className="ml-3 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-[#16A34A] transition hover:bg-[#DCFCE7]"
          aria-label="Kuponu kaldır"
        >
          <X size={15} />
        </button>
      </div>
    );
  }

  return (
    <div>
      <button
        type="button"
        onClick={handleToggle}
        className="flex w-full items-center justify-between gap-2 py-1 text-sm font-semibold text-[#6B7177] transition hover:text-[#2B2F36]"
      >
        <span className="flex items-center gap-2">
          <Tag size={14} className="text-[#FF7A00]" />
          Kupon kodum var
        </span>
        <ChevronDown
          size={15}
          className={cn("transition-transform duration-200", open && "rotate-180")}
        />
      </button>

      <div
        className={cn(
          "grid overflow-hidden transition-all duration-200",
          open ? "grid-rows-[1fr] opacity-100 pt-3" : "grid-rows-[0fr] opacity-0",
        )}
      >
        <div className="overflow-hidden">
          <div className="grid gap-2">
            <div className="flex gap-2">
              <Input
                ref={inputRef}
                value={code}
                onChange={(e) => {
                  setCode(e.target.value.toUpperCase());
                  if (error) setError(null);
                }}
                onKeyDown={(e) => {
                  if (e.key === "Enter") {
                    e.preventDefault();
                    handleApply();
                  }
                }}
                placeholder="KUPON KODU"
                maxLength={64}
                disabled={disabled || validating}
                className={cn(
                  "flex-1 font-mono tracking-widest placeholder:font-sans placeholder:not-italic placeholder:tracking-normal",
                  error && "border-[#EF4444] focus-visible:ring-[#EF4444]",
                )}
                autoComplete="off"
                spellCheck={false}
              />
              <Button
                type="button"
                onClick={handleApply}
                disabled={!code.trim() || disabled || validating}
                className="shrink-0 rounded-xl bg-[#2B2F36] px-5 text-sm font-bold text-white hover:bg-[#1A1D22] disabled:opacity-50"
              >
                {validating ? (
                  <Loader2 size={15} className="animate-spin" />
                ) : (
                  "Uygula"
                )}
              </Button>
            </div>
            {error ? (
              <p className="text-xs font-semibold text-[#EF4444]">{error}</p>
            ) : null}
          </div>
        </div>
      </div>
    </div>
  );
}
