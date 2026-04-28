import { performance } from "node:perf_hooks";

const config = {
  baseUrl: process.env.LOADTEST_BASE_URL ?? "http://localhost:3000",
  timeoutMs: Number(process.env.LOADTEST_TIMEOUT_MS ?? 15000),
  homeUsers: Number(process.env.LOADTEST_HOME_USERS ?? 250),
  productsPageUsers: Number(process.env.LOADTEST_PRODUCTS_PAGE_USERS ?? 250),
  catalogApiUsers: Number(process.env.LOADTEST_CATALOG_API_USERS ?? 1000),
  guestCartUsers: Number(process.env.LOADTEST_GUEST_CART_USERS ?? 1000),
  registerUsers: Number(process.env.LOADTEST_REGISTER_USERS ?? 150),
};

async function main() {
  console.log(`Base URL: ${config.baseUrl}`);

  const seed = await fetchJson("/api/v1/products?per_page=1&page=1");
  const productId = seed.data?.[0]?.id;

  if (!productId) {
    throw new Error("Load test icin kullanilabilir urun bulunamadi.");
  }

  console.log(`Seed product id: ${productId}`);

  const results = [];

  results.push(await runScenario("home_page", config.homeUsers, async () => {
    await fetchText("/");
  }));

  results.push(await runScenario("products_page", config.productsPageUsers, async () => {
    await fetchText("/products");
  }));

  results.push(await runScenario("catalog_api", config.catalogApiUsers, async (index) => {
    await fetchJson(`/api/v1/products?per_page=48&page=${(index % 4) + 1}`, {
      headers: {
        "X-Cart-Token": `catalog-${index}-${crypto.randomUUID()}`,
      },
    });
  }));

  results.push(await runScenario("guest_cart_flow", config.guestCartUsers, async (index) => {
    const cartToken = `guest-cart-${index}-${crypto.randomUUID()}`;

    await fetchJson("/api/v1/cart/items", {
      method: "POST",
      headers: {
        "X-Cart-Token": cartToken,
      },
      body: JSON.stringify({
        product_id: productId,
        quantity: 1 + (index % 3),
      }),
    });

    await fetchJson("/api/v1/cart", {
      headers: {
        "X-Cart-Token": cartToken,
      },
    });
  }));

  results.push(await runScenario("auth_register", config.registerUsers, async (index) => {
    const cartToken = `register-cart-${index}-${crypto.randomUUID()}`;
    const phoneSuffix = String(100_000_000 + index).slice(-9);

    await fetchJson("/api/v1/auth/register", {
      method: "POST",
      headers: {
        "X-Cart-Token": cartToken,
      },
      body: JSON.stringify({
        name: `Load Test User ${index + 1}`,
        phone: `5${phoneSuffix}`,
        password: "LoadTest123A",
        device_name: "loadtest",
        cart_token: cartToken,
      }),
    });
  }));

  console.log("");
  console.table(results.map((result) => ({
    scenario: result.name,
    requests: result.requests,
    success: result.success,
    failed: result.failed,
    avg_ms: result.avgMs.toFixed(1),
    p95_ms: result.p95Ms.toFixed(1),
    max_ms: result.maxMs.toFixed(1),
    rps: result.rps.toFixed(1),
  })));
}

async function runScenario(name, totalRequests, worker) {
  const timings = [];
  const failures = [];
  const startedAt = performance.now();

  const results = await Promise.allSettled(
    Array.from({ length: totalRequests }, async (_, index) => {
      const requestStartedAt = performance.now();

      try {
        await worker(index);
        timings.push(performance.now() - requestStartedAt);
      } catch (error) {
        timings.push(performance.now() - requestStartedAt);
        failures.push(error instanceof Error ? error.message : String(error));
      }
    }),
  );

  const durationMs = performance.now() - startedAt;
  const success = results.filter((result) => result.status === "fulfilled").length - failures.length;
  const failureCount = failures.length;
  const sorted = [...timings].sort((a, b) => a - b);

  const summary = {
    name,
    requests: totalRequests,
    success,
    failed: failureCount,
    avgMs: average(sorted),
    p95Ms: percentile(sorted, 0.95),
    maxMs: sorted.at(-1) ?? 0,
    rps: totalRequests / Math.max(durationMs / 1000, 0.001),
  };

  const failureExamples = failures.slice(0, 3);

  console.log(`\n[${name}] ${success}/${totalRequests} basarili, ${failureCount} hata`);

  if (failureExamples.length > 0) {
    console.log(`[${name}] ornek hatalar:`);
    for (const example of failureExamples) {
      console.log(`- ${example}`);
    }
  }

  return summary;
}

async function fetchJson(path, init = {}) {
  const response = await fetch(buildUrl(path), {
    ...init,
    headers: {
      Accept: "application/json",
      ...(init.body ? { "Content-Type": "application/json" } : {}),
      ...(init.headers ?? {}),
    },
    signal: AbortSignal.timeout(config.timeoutMs),
  });

  const payload = await response.json().catch(() => null);

  if (!response.ok) {
    throw new Error(`${path} -> ${response.status} ${extractMessage(payload)}`);
  }

  return payload;
}

async function fetchText(path, init = {}) {
  const response = await fetch(buildUrl(path), {
    ...init,
    signal: AbortSignal.timeout(config.timeoutMs),
  });

  if (!response.ok) {
    throw new Error(`${path} -> ${response.status}`);
  }

  return response.text();
}

function buildUrl(path) {
  return new URL(path, config.baseUrl).toString();
}

function extractMessage(payload) {
  if (!payload || typeof payload !== "object") {
    return "Bilinmeyen hata";
  }

  if (typeof payload.message === "string" && payload.message.trim() !== "") {
    return payload.message;
  }

  if (payload.errors && typeof payload.errors === "object") {
    for (const value of Object.values(payload.errors)) {
      if (Array.isArray(value) && value[0]) {
        return String(value[0]);
      }

      if (typeof value === "string" && value.trim() !== "") {
        return value;
      }
    }
  }

  return "Bilinmeyen hata";
}

function average(values) {
  if (values.length === 0) {
    return 0;
  }

  return values.reduce((sum, value) => sum + value, 0) / values.length;
}

function percentile(values, ratio) {
  if (values.length === 0) {
    return 0;
  }

  const index = Math.min(values.length - 1, Math.max(0, Math.ceil(values.length * ratio) - 1));

  return values[index];
}

main().catch((error) => {
  console.error(error instanceof Error ? error.message : error);
  process.exitCode = 1;
});
