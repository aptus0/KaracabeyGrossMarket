"use client";

import { useEffect } from "react";
import { AnimatePresence, motion } from "framer-motion";
import { CheckCircle2, X } from "lucide-react";
import { useCartStore } from "@/lib/cart-store";
import { Button } from "@/app/_components/ui/button";

export function CartNotification() {
  const { lastAddedItem, clearLastAddedItem, openSheet } = useCartStore();

  useEffect(() => {
    if (lastAddedItem) {
      const timer = setTimeout(() => {
        clearLastAddedItem();
      }, 4000); // 4 seconds
      return () => clearTimeout(timer);
    }
  }, [lastAddedItem, clearLastAddedItem]);

  return (
    <AnimatePresence>
      {lastAddedItem && (
        <motion.div
          initial={{ opacity: 0, y: -50, scale: 0.95 }}
          animate={{ opacity: 1, y: 0, scale: 1 }}
          exit={{ opacity: 0, y: -20, scale: 0.95 }}
          transition={{ type: "spring", stiffness: 400, damping: 25 }}
          className="fixed top-24 right-4 z-50 w-full max-w-sm rounded-2xl bg-white p-4 shadow-2xl ring-1 ring-black/5 md:right-8 lg:right-12"
        >
          <div className="flex items-start gap-4">
            <div className="flex-shrink-0 pt-0.5">
              <CheckCircle2 className="h-10 w-10 text-[#FF7A00]" />
            </div>
            <div className="flex-1">
              <p className="text-sm font-medium text-gray-900">
                Ürün sepete eklendi
              </p>
              <p className="mt-1 text-sm text-gray-500 line-clamp-1">
                {lastAddedItem.product.name}
              </p>
              <div className="mt-2 flex items-center gap-3">
                <span className="text-lg font-bold text-gray-900">
                  {new Intl.NumberFormat("tr-TR", {
                    style: "currency",
                    currency: "TRY",
                  }).format(lastAddedItem.product.price_cents / 100)}
                </span>
                <span className="text-sm text-gray-500">
                  Adet: {lastAddedItem.quantity}
                </span>
              </div>
              <div className="mt-4 flex gap-2">
                <Button
                  onClick={() => {
                    clearLastAddedItem();
                    openSheet();
                  }}
                  className="flex-1 bg-[#FF7A00] hover:bg-[#E66E00] text-white rounded-xl"
                >
                  Sepete Git
                </Button>
              </div>
            </div>
            <div className="flex-shrink-0">
              <button
                type="button"
                onClick={clearLastAddedItem}
                className="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FF7A00] focus:ring-offset-2"
              >
                <span className="sr-only">Kapat</span>
                <X className="h-5 w-5" aria-hidden="true" />
              </button>
            </div>
          </div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}
