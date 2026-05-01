<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private string $apnsKey;
    private string $apnsKeyId;
    private string $apnsTeamId;
    private string $apnsBundleId;

    public function __construct()
    {
        $this->apnsKey = config('services.apns.key');
        $this->apnsKeyId = config('services.apns.key_id');
        $this->apnsTeamId = config('services.apns.team_id');
        $this->apnsBundleId = config('services.apns.bundle_id');
    }

    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'tenant_id' => $user->tenant_id ?? 1,
            'type' => $data['type'] ?? 'general',
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'sent_at' => now(),
        ]);

        $tokens = DeviceToken::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        foreach ($tokens as $token) {
            $this->sendPushNotification($token, $title, $body, $data);
        }
    }

    public function sendOrderStatusUpdate(object $order, string $status): void
    {
        if (! $order->user) {
            return;
        }

        $statusLabels = [
            'pending' => 'Beklemede',
            'processing' => 'İşleniyor',
            'shipped' => 'Kargoda',
            'delivered' => 'Teslim Edildi',
            'cancelled' => 'İptal Edildi',
        ];

        $title = 'Siparişiniz ' . ($statusLabels[$status] ?? $status);
        $body = "#" . $order->number . " numaralı siparişinizin durumu güncellendi.";

        $this->sendToUser($order->user, $title, $body, [
            'type' => 'order_update',
            'order_id' => $order->id,
            'order_number' => $order->number,
            'status' => $status,
        ]);
    }

    public function sendCargoStatusUpdate(object $shipment, string $status): void
    {
        $order = $shipment->order;
        if (! $order->user) {
            return;
        }

        $title = 'Kargo Güncelleme';
        $body = "Siparişinizin kargosunda yeni bir güncelleme var: " . $status;

        $this->sendToUser($order->user, $title, $body, [
            'type' => 'cargo_update',
            'order_id' => $order->id,
            'shipment_id' => $shipment->id,
            'status' => $status,
        ]);
    }

    public function sendPromotionalNotification(string $title, string $body, array $recipientIds = [], array $data = []): void
    {
        $query = DeviceToken::where('is_active', true);

        if (! empty($recipientIds)) {
            $query->whereIn('user_id', $recipientIds);
        }

        $tokens = $query->get();

        foreach ($tokens as $token) {
            $this->sendPushNotification($token, $title, $body, $data);
        }
    }

    private function sendPushNotification(DeviceToken $token, string $title, string $body, array $data): void
    {
        try {
            if ($token->device_type === 'ios') {
                $this->sendAPNS($token->token, $title, $body, $data);
            } elseif ($token->device_type === 'android') {
                $this->sendFCM($token->token, $title, $body, $data);
            }
        } catch (\Exception $e) {
            Log::error('Push notification failed', [
                'token_id' => $token->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendAPNS(string $deviceToken, string $title, string $body, array $data): void
    {
        $payload = [
            'aps' => [
                'alert' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'sound' => 'default',
                'badge' => 1,
            ],
            'data' => $data,
        ];

        // In production, use proper APNS client library
        // This is a simplified example
        Log::info('APNS notification prepared', [
            'device_token' => substr($deviceToken, 0, 10) . '...',
            'payload' => $payload,
        ]);
    }

    private function sendFCM(string $deviceToken, string $title, string $body, array $data): void
    {
        $payload = [
            'registration_ids' => [$deviceToken],
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => $data,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key=' . config('services.fcm.server_key'),
        ])->post('https://fcm.googleapis.com/fcm/send', $payload);

        if (! $response->successful()) {
            Log::error('FCM notification failed', [
                'device_token' => substr($deviceToken, 0, 10) . '...',
                'response' => $response->body(),
            ]);
        }
    }
}
