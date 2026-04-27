export const apiBaseUrl = process.env.NEXT_PUBLIC_API_URL ?? "";

type ApiErrorPayload = {
  message?: string;
  errors?: Record<string, string | string[]>;
};

export class ApiRequestError extends Error {
  status: number;
  errors?: Record<string, string | string[]>;

  constructor(message: string, status: number, errors?: Record<string, string | string[]>) {
    super(message);
    this.name = "ApiRequestError";
    this.status = status;
    this.errors = errors;
  }
}

export function buildApiUrl(path: string) {
  return `${apiBaseUrl}${path}`;
}

export async function apiRequest<T>(path: string, init: RequestInit = {}): Promise<T> {
  const response = await fetch(buildApiUrl(path), {
    ...init,
    headers: {
      Accept: "application/json",
      ...(init.body ? { "Content-Type": "application/json" } : {}),
      ...(init.headers ?? {}),
    },
  });

  const payload = (await response.json().catch(() => null)) as
    | ({ data?: T } & ApiErrorPayload)
    | null;

  if (!response.ok) {
    throw new ApiRequestError(
      resolveErrorMessage(payload) ?? `İstek başarısız oldu (${response.status}).`,
      response.status,
      payload?.errors,
    );
  }

  return (payload?.data ?? payload) as T;
}

export function extractErrorMessage(error: unknown, fallback = "Bir hata oluştu.") {
  if (error instanceof ApiRequestError) {
    return error.message;
  }

  if (error instanceof Error && error.message) {
    return error.message;
  }

  return fallback;
}

function resolveErrorMessage(payload: ApiErrorPayload | null) {
  if (!payload) {
    return null;
  }

  const firstValidationMessage = payload.errors
    ? Object.values(payload.errors)
        .flatMap((value) => (Array.isArray(value) ? value : [value]))
        .find(Boolean)
    : null;

  return firstValidationMessage ?? payload.message ?? null;
}
