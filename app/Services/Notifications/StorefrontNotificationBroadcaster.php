<?php

namespace App\Services\Notifications;

use App\Models\NotificationBroadcast;
use App\Models\User;
use App\Notifications\StorefrontNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StorefrontNotificationBroadcaster
{
    public function deliver(NotificationBroadcast $broadcast): int
    {
        $delivered = 0;

        $this->audienceQuery($broadcast)
            ->orderBy('id')
            ->chunkById(100, function (Collection $users) use ($broadcast, &$delivered): void {
                foreach ($users as $user) {
                    $user->notify(new StorefrontNotification([
                        'type' => $broadcast->type,
                        'title' => $broadcast->title,
                        'body' => $broadcast->body,
                        'action_url' => $broadcast->action_url,
                        'image_url' => $broadcast->image_url,
                        'payload' => $broadcast->payload,
                        'broadcast_id' => $broadcast->id,
                    ]));

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
