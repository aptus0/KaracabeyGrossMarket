"use client";

import { useEffect, useState } from "react";
import { AnimatePresence, motion } from "framer-motion";
import { X } from "lucide-react";
import { Button } from "@/app/_components/ui/button";

export function CampaignModal() {
  const [isOpen, setIsOpen] = useState(false);

  useEffect(() => {
    // Sadece bir kere göstermek için sessionStorage kullanıyoruz
    const hasSeenModal = sessionStorage.getItem("kgm-campaign-modal-seen");
    if (!hasSeenModal) {
      // Sayfa yüklendikten kısa bir süre sonra göster (daha iyi UX için)
      const timer = setTimeout(() => {
        setIsOpen(true);
      }, 1000);
      return () => clearTimeout(timer);
    }
  }, []);

  function handleClose() {
    setIsOpen(false);
    sessionStorage.setItem("kgm-campaign-modal-seen", "true");
  }

  return (
    <AnimatePresence>
      {isOpen && (
        <>
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm"
            onClick={handleClose}
          />
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            <motion.div
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              transition={{ type: "spring", stiffness: 300, damping: 30 }}
              className="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl"
            >
              <button
                type="button"
                onClick={handleClose}
                className="absolute right-4 top-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-black/10 text-gray-600 transition-colors hover:bg-black/20"
              >
                <X size={18} />
              </button>
              
              <div className="aspect-[16/9] w-full bg-gradient-to-br from-[#FF7A00] to-[#E66E00] flex items-center justify-center p-8 text-center text-white">
                <div>
                  <h2 className="text-3xl font-black tracking-tight mb-2">Hoş Geldiniz!</h2>
                  <p className="text-lg text-white/90">
                    Karacabey Gross Market'e özel avantajlı fiyatları kaçırmayın.
                  </p>
                </div>
              </div>

              <div className="p-6 text-center">
                <p className="text-gray-600 mb-6">
                  Hemen alışverişe başlayın, binlerce üründe toptan fiyatına perakende alışveriş fırsatını yakalayın!
                </p>
                <Button
                  onClick={handleClose}
                  className="w-full rounded-xl bg-[#FF7A00] py-6 text-lg font-bold hover:bg-[#E66E00]"
                >
                  Alışverişe Başla
                </Button>
              </div>
            </motion.div>
          </div>
        </>
      )}
    </AnimatePresence>
  );
}
