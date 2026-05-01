type KgmLogoProps = {
  compact?: boolean;
  variant?: "full" | "app";
};

export function KgmLogo({ compact = false, variant = "full" }: KgmLogoProps) {
  if (variant === "app") {
    return (
      <span className={compact ? "kgm-logo kgm-logo--app kgm-logo--compact" : "kgm-logo kgm-logo--app"}>
        <img
          src="/assets/kgm-favicon-256.png"
          alt="Karacabey Gross Market"
        />
      </span>
    );
  }

  return (
    <span className={compact ? "kgm-logo kgm-logo--compact" : "kgm-logo"}>
      <img
        src="/assets/kg-web.png"
        alt="Karacabey Gross Market"
        width={1600}
        height={460}
      />
    </span>
  );
}
