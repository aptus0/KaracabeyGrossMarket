<?php

namespace App\Services\Notifications;

use App\Models\NotificationBroadcast;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StorefrontNotificationBroadcaster
{
    public function __construct(private readonly PushNotificationService $pushNotifications) {}

    public function deliver(NotificationBroadcast $broadcast): int
    {
        $delivered = 0;

        $this->audienceQuery($broadcast)
            ->orderBy('id')
            ->chunkById(100, function (Collection $users) use ($broadcast, &$delivered): void {
                foreach ($users as $user) {
                    $this->pushNotifications->sendToUser(
                        $user,
                        $broadcast->title,
                        $broadcast->body,
                        [
                            'type' => $broadcast->type,
                            'action_url' => $broadcast->action_url,
                            'image_url' => $broadcast->image_url,
                            'broadcast_id' => $broadcast->id,
                            'payload' => $broadcast->payload ?? [],
                            'tenant_id' => $broadcast->tenant_id,
                        ],
                        $broadcast->tenant_id,
                    );

                    $delivered++;
                }
            });

        $broadcast->forceFill(['delivered_count' => $delivered])->save();

        return $delivered;
    }

    private function audienceQuery(NotificationBroadcast $broadcast): Builder
    {
        $query = User::query()->where('is_admin', false);

        if ($broadcast->audience === 'user' && $broadcast->target_user_id) {
            $query->whereKey($broadcast->target_user_id);
        }

        return $query;
    }
}
