<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\Notification;
use App\Models\Tenant;
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

    public function sendToUser(
        User $user,
        string $title,
        string $body,
        array $data = [],
        ?int $tenantId = null,
    ): Notification
    {
        $payload = $this->normalizePayload($data);

        $notification = Notification::create([
            'user_id' => $user->id,
            'tenant_id' => $tenantId ?? $this->resolveTenantId($payload),
            'type' => $payload['type'] ?? 'general',
            'title' => $title,
            'body' => $body,
            'data' => $payload,
            'sent_at' => now(),
        ]);

        $tokens = DeviceToken::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        foreach ($tokens as $token) {
            $this->sendPushNotification($token, $title, $body, $payload);
        }

        return $notification;
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

    private function normalizePayload(array $data): array
    {
        $payload = $data;

        if (! isset($payload['payload']) || ! is_array($payload['payload'])) {
            $payload['payload'] = [];
        }

        return $payload;
    }

    private function resolveTenantId(array $data): int
    {
        if (isset($data['tenant_id']) && is_numeric($data['tenant_id'])) {
            return (int) $data['tenant_id'];
        }

        return (int) (Tenant::query()->where('slug', 'karacabey-gross-market')->value('id')
            ?? Tenant::query()->value('id')
            ?? 1);
    }

    private function sendPushNotification(DeviceToken $token, string $title, string $body, array $data): void
    {
        try {
            $pushData = $this->sanitizePushData($data);

            if ($token->device_type === 'ios') {
                $this->sendAPNS($token->token, $title, $body, $pushData);
            } elseif ($token->device_type === 'android') {
                $this->sendFCM($token->token, $title, $body, $pushData);
            }
        } catch (\Exception $e) {
            Log::error('Push notification failed', [
                'token_id' => $token->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sanitizePushData(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $sanitized[$key] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                continue;
            }

            if (is_bool($value)) {
                $sanitized[$key] = $value ? '1' : '0';
                continue;
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    private function sendAPNS(string $deviceToken, string $title, string $body, array $data): void
    {
        $keyPath = $this->apnsKey;

        if (! file_exists($keyPath)) {
            Log::warning('APNS key file not found', ['path' => $keyPath]);
            return;
        }

        try {
            $authentication = new \Pushok\AuthenticationToken(
                file_get_contents($keyPath),
                $this->apnsKeyId,
                $this->apnsTeamId
            );

            $notification = new \Pushok\Notification();
            $notification
                ->setDeviceToken($deviceToken)
                ->setAlert(['title' => $title, 'body' => $body])
                ->setSound('default')
                ->setBadge(1);

            if (! empty($data)) {
                foreach ($data as $key => $value) {
                    $notification->setCustomValue($key, $value);
                }
            }

            $client = new \Pushok\Client($authentication);
            $client->isProduction(config('services.apns.production') ?? false);
            $client->push($notification);

            Log::info('APNS notification sent', [
                'device_token' => substr($deviceToken, 0, 10) . '...',
            ]);
        } catch (\Exception $e) {
            Log::error('APNS notification error', [
                'error' => $e->getMessage(),
                'device_token' => substr($deviceToken, 0, 10) . '...',
            ]);
        }
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
