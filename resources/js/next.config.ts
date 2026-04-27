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

const backendOrigin = stripTrailingSlash(
  process.env.NEXT_PUBLIC_API_URL
    ?? process.env.API_URL
    ?? process.env.APP_URL
    ?? readLaravelEnvValue("API_URL")
    ?? readLaravelEnvValue("APP_URL")
    ?? "http://127.0.0.1:8000",
);

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
