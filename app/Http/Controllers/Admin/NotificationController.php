<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationBroadcast;
use App\Models\User;
use App\Services\Notifications\StorefrontNotificationBroadcaster;
use App\Support\TenantResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request, TenantResolver $tenants): View
    {
        $tenant = $tenants->resolve($request);

        return view('admin.notifications.index', [
            'users' => User::query()
                ->where('is_admin', false)
                ->orderBy('name')
                ->get(['id', 'name', 'phone', 'email']),
            'broadcasts' => NotificationBroadcast::query()
                ->where('tenant_id', $tenant->id)
                ->with(['creator', 'targetUser'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function store(
        Request $request,
        TenantResolver $tenants,
        StorefrontNotificationBroadcaster $broadcaster,
    ): RedirectResponse {
        $tenant = $tenants->resolve($request);
        $validated = $request->validate([
            'audience' => ['required', Rule::in(['all', 'user'])],
            'target_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'type' => ['required', Rule::in(['general', 'campaign', 'product'])],
            'title' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:2000'],
            'action_url' => ['nullable', 'string', 'max:500'],
            'image_url' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['audience'] === 'user' && empty($validated['target_user_id'])) {
            return back()->withErrors(['target_user_id' => 'Tek kullanıcı hedefi için müşteri seçin.'])->withInput();
        }

        $broadcast = NotificationBroadcast::query()->create([
            'tenant_id' => $tenant->id,
            'created_by' => $request->user()?->id,
            'target_user_id' => $validated['audience'] === 'user' ? $validated['target_user_id'] : null,
            'audience' => $validated['audience'],
            'type' => $validated['type'],
            'title' => trim($validated['title']),
            'body' => trim($validated['body']),
            'action_url' => $this->normalizeActionUrl($validated['action_url'] ?? null),
            'image_url' => $validated['image_url'] ?: null,
            'payload' => [
                'tenant' => $tenant->slug,
            ],
        ]);

        $count = $broadcaster->deliver($broadcast);

        return redirect()->route('admin.notifications.index')
            ->with('status', "{$count} kullanıcıya bildirim gönderildi.");
    }

    private function normalizeActionUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }
}
