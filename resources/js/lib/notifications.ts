import { apiRequest } from "@/lib/api";

export type StorefrontNotificationItem = {
  id: string;
  type: string;
  title: string;
  body: string;
  action_url?: string | null;
  image_url?: string | null;
  payload?: Record<string, unknown> | null;
  broadcast_id?: number | null;
  read_at?: string | null;
  created_at?: string | null;
};

type NotificationIndexPayload = {
  data: StorefrontNotificationItem[];
  meta?: {
    unread_count?: number;
  };
};

function authHeaders(token: string) {
  return {
    Authorization: `Bearer ${token}`,
  };
}

export async function fetchUserNotifications(token: string, limit = 25) {
  return apiRequest<NotificationIndexPayload>(`/api/v1/notifications?limit=${limit}`, {
    headers: authHeaders(token),
  });
}

export async function markNotificationRead(token: string, notificationId: string) {
  return apiRequest<StorefrontNotificationItem>(`/api/v1/notifications/${notificationId}/read`, {
    method: "POST",
    headers: authHeaders(token),
  });
}

export async function markAllNotificationsRead(token: string) {
  return apiRequest<{ status: string }>("/api/v1/notifications/read-all", {
    method: "POST",
    headers: authHeaders(token),
  });
}
