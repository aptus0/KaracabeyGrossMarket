<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $user = $request->user();
        $limit = (int) ($validated['limit'] ?? 25);
        $notifications = $user->notifications()->latest()->limit($limit)->get();

        return response()->json([
            'data' => $notifications->map(fn (DatabaseNotification $notification): array => $this->serialize($notification))->values(),
            'meta' => [
                'unread_count' => $user->unreadNotifications()->count(),
            ],
        ]);
    }

    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        $notification = $request->user()->notifications()->whereKey($notificationId)->firstOrFail();

        if (! $notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json([
            'data' => $this->serialize($notification->fresh()),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'data' => ['status' => 'ok'],
        ]);
    }

    public function storeDeviceToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'in:ios,android,web,mobile'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $deviceToken = UserDeviceToken::query()->updateOrCreate(
            ['token' => $validated['token']],
            [
                'user_id' => $request->user()->id,
                'platform' => $validated['platform'] ?? 'mobile',
                'device_name' => $validated['device_name'] ?? null,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'data' => [
                'id' => $deviceToken->id,
                'status' => 'registered',
            ],
        ], 201);
    }

    private function serialize(DatabaseNotification $notification): array
    {
        $data = is_array($notification->data) ? $notification->data : [];

        return [
            'id' => $notification->id,
            'type' => $data['type'] ?? 'general',
            'title' => $data['title'] ?? 'Bildirim',
            'body' => $data['body'] ?? '',
            'action_url' => $data['action_url'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'payload' => $data['payload'] ?? null,
            'broadcast_id' => $data['broadcast_id'] ?? null,
            'read_at' => $notification->read_at?->toIso8601String(),
            'created_at' => $notification->created_at?->toIso8601String(),
        ];
    }
}
