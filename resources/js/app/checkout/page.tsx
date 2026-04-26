import type { Metadata } from "next";
import { ShellHeader } from "@/app/_components/ShellHeader";
import { formatPrice, products } from "@/lib/catalog";

export const metadata: Metadata = {
  title: "Checkout",
  robots: {
    index: false,
    follow: false,
  },
};

const cart = products.slice(0, 3).map((product, index) => ({
  ...product,
  quantity: index + 1,
}));

const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

export default function CheckoutPage() {
  return (
    <>
      <ShellHeader />
      <main className="s30">
        <section className="s31">
          <p className="s7">Güvenli ödeme</p>
          <h1 className="s16">Checkout</h1>
          <form className="s33" action={`${process.env.NEXT_PUBLIC_API_URL ?? ""}/api/v1/checkout/paytr`} method="post">
            <label>
              Ad Soyad
              <input name="customer[name]" autoComplete="name" required />
            </label>
            <label>
              E-posta
              <input name="customer[email]" type="email" autoComplete="email" required />
            </label>
            <label>
              Telefon
              <input name="customer[phone]" autoComplete="tel" required />
            </label>
            <label>
              Teslimat Adresi
              <textarea name="shipping[address]" autoComplete="street-address" required />
            </label>
            {cart.map((item, index) => (
              <input
                key={item.slug}
                type="hidden"
                name={`items[${index}][product_id]`}
                value={products.findIndex((product) => product.slug === item.slug) + 1}
              />
            ))}
            {cart.map((item, index) => (
              <input
                key={`${item.slug}-qty`}
                type="hidden"
                name={`items[${index}][quantity]`}
                value={item.quantity}
              />
            ))}
            <button className="s39" type="submit">
              PayTR ile Ödemeye Devam Et
            </button>
          </form>
        </section>

        <aside className="s37">
          <p className="s7">Sepet</p>
          <ul className="s35">
            {cart.map((item) => (
              <li key={item.slug}>
                <span>
                  {item.name} x {item.quantity}
                </span>
                <strong>{formatPrice(item.price * item.quantity)}</strong>
              </li>
            ))}
          </ul>
          <div className="s38">
            <span>Toplam</span>
            <strong>{formatPrice(total)}</strong>
          </div>
          <p className="s40">
            Kart bilgileri Karacabey Gross Market sunucularına uğramadan PayTR güvenli ödeme ekranında işlenir.
          </p>
        </aside>
      </main>
    </>
  );
}
