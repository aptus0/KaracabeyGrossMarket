type PaymentIframeBoxProps = {
  checkoutUrl?: string;
};

export function PaymentIframeBox({ checkoutUrl }: PaymentIframeBoxProps) {
  return (
    <section className="payment-box" aria-label="PayTR ödeme">
      <div>
        <strong>PayTR Güvenli Ödeme</strong>
        <p>Kart bilgileriniz PayTR güvenli ödeme ekranında işlenir.</p>
      </div>
      {checkoutUrl ? (
        <iframe src={checkoutUrl} title="PayTR güvenli ödeme formu" />
      ) : null}
    </section>
  );
}
