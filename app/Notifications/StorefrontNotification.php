<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StorefrontNotification extends Notification
{
    use Queueable;

    /**
     * @param  array{
     *   type?: string,
     *   title: string,
     *   body: string,
     *   action_url?: string|null,
     *   image_url?: string|null,
     *   payload?: array<string, mixed>|null,
     *   broadcast_id?: int|null
     * }  $payload
     */
    public function __construct(private readonly array $payload) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->payload['type'] ?? 'general',
            'title' => $this->payload['title'],
            'body' => $this->payload['body'],
            'action_url' => $this->payload['action_url'] ?? null,
            'image_url' => $this->payload['image_url'] ?? null,
            'payload' => $this->payload['payload'] ?? null,
            'broadcast_id' => $this->payload['broadcast_id'] ?? null,
            'sent_at' => now()->toIso8601String(),
        ];
    }
}
