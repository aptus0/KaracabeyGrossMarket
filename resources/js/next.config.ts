import type { NextConfig } from "next";
import { existsSync, readFileSync } from "node:fs";
import { resolve } from "node:path";

function readLaravelEnvValue(key: string) {
  const envPath = resolve(process.cwd(), "..", "..", ".env");

  if (!existsSync(envPath)) {
    return null;
  }

  const file = readFileSync(envPath, "utf8");

  for (const rawLine of file.split(/\r?\n/)) {
    const line = rawLine.trim();

    if (!line || line.startsWith("#") || !line.startsWith(`${key}=`)) {
      continue;
    }

    const value = line.slice(key.length + 1).trim();

    return value.replace(/^['"]|['"]$/g, "");
  }

  return null;
}

function stripTrailingSlash(value: string | null | undefined) {
  return value ? value.replace(/\/+$/, "") : "";
}

function normalizeOrigin(value: string | null | undefined) {
  const origin = stripTrailingSlash(value);

  if (!origin) {
    return "";
  }

  try {
    const parsed = new URL(origin);

    if (parsed.protocol === "http:" && parsed.hostname.endsWith(".test")) {
      parsed.protocol = "https:";
    }

    return stripTrailingSlash(parsed.toString());
  } catch {
    return origin;
  }
}

function toRemotePattern(origin: string | null | undefined) {
  const normalized = normalizeOrigin(origin);

  if (!normalized) {
    return null;
  }

  try {
    const parsed = new URL(normalized);

    return {
      protocol: parsed.protocol.replace(":", "") as "http" | "https",
      hostname: parsed.hostname,
      port: parsed.port || undefined,
    };
  } catch {
    return null;
  }
}

type RemotePattern = NonNullable<ReturnType<typeof toRemotePattern>>;

const internalApiOrigin = process.env.INTERNAL_API_URL
  ?? process.env.API_INTERNAL_URL
  ?? readLaravelEnvValue("API_INTERNAL_URL")
  ?? process.env.API_URL
  ?? readLaravelEnvValue("API_URL")
  ?? "http://127.0.0.1:8000";

const publicApiOrigin = process.env.NEXT_PUBLIC_API_URL
  ?? process.env.API_URL
  ?? readLaravelEnvValue("API_URL")
  ?? "https://api.karacabeygrossmarket.com";

const storefrontOrigin = process.env.NEXT_PUBLIC_SITE_URL
  ?? process.env.STOREFRONT_URL
  ?? readLaravelEnvValue("STOREFRONT_URL")
  ?? process.env.FRONTEND_URL
  ?? readLaravelEnvValue("FRONTEND_URL")
  ?? "https://karacabeygrossmarket.com";

const cdnOrigin = process.env.NEXT_PUBLIC_CDN_URL
  ?? process.env.CDN_URL
  ?? readLaravelEnvValue("CDN_URL")
  ?? "";

const remotePatterns: RemotePattern[] = [
  {
    protocol: "https",
    hostname: "images.unsplash.com",
    port: undefined,
  },
  ...[storefrontOrigin, publicApiOrigin, cdnOrigin]
    .map(toRemotePattern)
    .filter((pattern): pattern is RemotePattern => pattern !== null),
];

const nextConfig: NextConfig = {
  turbopack: {
    root: __dirname,
  },
  env: {
    NEXT_PUBLIC_SITE_URL: normalizeOrigin(storefrontOrigin),
    NEXT_PUBLIC_API_URL: normalizeOrigin(publicApiOrigin),
    NEXT_PUBLIC_CDN_URL: normalizeOrigin(cdnOrigin),
  },
  assetPrefix: process.env.NODE_ENV === "production" && cdnOrigin ? normalizeOrigin(cdnOrigin) : undefined,
  images: {
    remotePatterns,
  },
  async rewrites() {
    return [
      {
        source: "/api/:path*",
        destination: `${normalizeOrigin(internalApiOrigin)}/api/:path*`,
      },
      {
        source: "/oauth/:path*",
        destination: `${normalizeOrigin(internalApiOrigin)}/oauth/:path*`,
      },
    ];
  },
};

export default nextConfig;
