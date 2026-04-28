type KgmLogoProps = {
  compact?: boolean;
};

export function KgmLogo({ compact = false }: KgmLogoProps) {
  return (
    <span className={compact ? "kgm-logo kgm-logo--compact" : "kgm-logo"}>
      <img
        src="/assets/kgm-logo.png"
        alt="Karacabey Gross Market"
        width={1400}
        height={742}
      />
    </span>
  );
}
