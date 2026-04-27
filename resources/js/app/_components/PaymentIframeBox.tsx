type PaymentIframeBoxProps = {
  checkoutUrl?: string;
};

export function PaymentIframeBox({ checkoutUrl }: PaymentIframeBoxProps) {
  return (
    <section className="payment-box" aria-label="Güvenli ödeme">
      <div>
        <strong>Güvenli Ödeme</strong>
        <p>Kart bilgileriniz güvenli ödeme ekranında işlenir.</p>
      </div>
      {checkoutUrl ? (
        <iframe src={checkoutUrl} title="Güvenli ödeme formu" />
      ) : null}
    </section>
  );
}
