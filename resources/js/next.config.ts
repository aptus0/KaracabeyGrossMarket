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

function normalizeBackendOrigin(value: string | null | undefined) {
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

function isLocalTestOrigin(value: string | null | undefined) {
  if (!value) {
    return false;
  }

  try {
    return new URL(value).hostname.endsWith(".test");
  } catch {
    return false;
  }
}

const explicitApiOrigin = process.env.NEXT_PUBLIC_API_URL
  ?? process.env.API_URL
  ?? readLaravelEnvValue("API_URL");

const appOrigin = process.env.APP_URL
  ?? readLaravelEnvValue("APP_URL");

const backendOrigin = explicitApiOrigin
  ? normalizeBackendOrigin(explicitApiOrigin)
  : isLocalTestOrigin(appOrigin)
    ? "http://127.0.0.1:8000"
    : normalizeBackendOrigin(appOrigin ?? "http://127.0.0.1:8000");

const nextConfig: NextConfig = {
  turbopack: {
    root: __dirname,
  },
  images: {
    remotePatterns: [
      {
        protocol: "https",
        hostname: "images.unsplash.com",
      },
    ],
  },
  async rewrites() {
    return [
      {
        source: "/api/:path*",
        destination: `${backendOrigin}/api/:path*`,
      },
      {
        source: "/oauth/:path*",
        destination: `${backendOrigin}/oauth/:path*`,
      },
    ];
  },
};

export default nextConfig;
