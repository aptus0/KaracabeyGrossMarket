type KgmLogoProps = {
  compact?: boolean;
  variant?: "full" | "app";
};

export function KgmLogo({ compact = false, variant = "full" }: KgmLogoProps) {
  if (variant === "app") {
    return (
      <span className={compact ? "kgm-logo kgm-logo--app kgm-logo--compact" : "kgm-logo kgm-logo--app"}>
        <img
          src="/assets/kg-light.png"
          alt="Karacabey Gross Market Logo"
          className="w-full h-full object-contain"
        />
      </span>
    );
  }

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
