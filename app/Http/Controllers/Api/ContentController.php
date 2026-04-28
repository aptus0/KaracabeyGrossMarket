<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\HomepageBlock;
use App\Models\NavigationItem;
use App\Models\Page;
use App\Support\TenantResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function homepage(Request $request, TenantResolver $tenants): JsonResponse
    {
        $tenant = $tenants->resolve($request);

        return response()->json([
            'data' => [
                'blocks' => HomepageBlock::query()
                    ->whereBelongsTo($tenant)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->map(fn (HomepageBlock $block): array => $this->serializeBlock($block))
                    ->values(),
                'campaigns' => $this->activeCampaigns($tenant->id)->map(fn (Campaign $campaign): array => $this->serializeCampaign($campaign))->values(),
            ],
        ]);
    }

    public function pages(Request $request, TenantResolver $tenants): JsonResponse
    {
        $tenant = $tenants->resolve($request);

        $pages = Page::query()
            ->whereBelongsTo($tenant)
            ->where('is_published', true)
            ->orderBy('title')
            ->get()
            ->map(fn (Page $page): array => $this->serializePage($page, includeBody: false))
            ->values();

        return response()->json(['data' => $pages]);
    }

    public function page(Request $request, TenantResolver $tenants, string $slug): JsonResponse
    {
        $tenant = $tenants->resolve($request);

        $page = Page::query()
            ->whereBelongsTo($tenant)
            ->where('is_published', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json(['data' => $this->serializePage($page)]);
    }

    public function campaigns(Request $request, TenantResolver $tenants): JsonResponse
    {
        $tenant = $tenants->resolve($request);

        return response()->json([
            'data' => $this->activeCampaigns($tenant->id)
                ->map(fn (Campaign $campaign): array => $this->serializeCampaign($campaign))
                ->values(),
        ]);
    }

    public function campaign(Request $request, TenantResolver $tenants, string $slug): JsonResponse
    {
        $tenant = $tenants->resolve($request);

        $campaign = Campaign::query()
            ->where('tenant_id', $tenant->id)
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json(['data' => $this->serializeCampaign($campaign, full: true)]);
    }

    public function marketing(Request $request, TenantResolver $tenants): JsonResponse
    {
        $tenant = $tenants->resolve($request);
        $setting = $tenant->marketingSetting()->first();

        return response()->json([
            'data' => [
                'google_analytics_id' => $setting?->google_analytics_id ?: config('services.google.analytics_id'),
                'google_ads_id' => $setting?->google_ads_id ?: config('services.google.ads_id'),
                'google_ads_conversion_label' => $setting?->google_ads_conversion_label ?: config('services.google.ads_conversion_label'),
                'google_site_verification' => $setting?->google_site_verification ?: config('services.google.site_verification'),
                'meta_pixel_id' => $setting?->meta_pixel_id ?: config('services.meta.pixel_id'),
            ],
        ]);
    }

    public function navigation(Request $request, TenantResolver $tenants): JsonResponse
    {
        $tenant = $tenants->resolve($request);

        $items = NavigationItem::query()
            ->whereBelongsTo($tenant)
            ->where('is_active', true)
            ->orderBy('placement')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get()
            ->groupBy('placement')
            ->map(fn ($items) => $items->map(fn (NavigationItem $item): array => $this->serializeNavigationItem($item))->values());

        return response()->json([
            'data' => collect(array_keys(NavigationItem::PLACEMENTS))
                ->mapWithKeys(fn (string $placement): array => [$placement => $items->get($placement, collect())->values()])
                ->all(),
        ]);
    }

    private function activeCampaigns(int $tenantId)
    {
        return Campaign::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->latest()
            ->get();
    }

    private function serializePage(Page $page, bool $includeBody = true): array
    {
        return [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'group' => $page->group,
            'body' => $includeBody ? $page->body : null,
            'seo' => [
                'title' => $page->seo_title ?: $page->title,
                'description' => $page->seo_description,
                'image_url' => $page->meta_image_url,
            ],
        ];
    }

    private function serializeBlock(HomepageBlock $block): array
    {
        return [
            'id' => $block->id,
            'type' => $block->type,
            'title' => $block->title,
            'subtitle' => $block->subtitle,
            'image_url' => $block->image_url,
            'link_url' => $block->link_url,
            'link_label' => $block->link_label,
            'payload' => $block->payload,
        ];
    }

    private function serializeCampaign(Campaign $campaign, bool $full = false): array
    {
        $data = [
            'id'               => $campaign->id,
            'name'             => $campaign->name,
            'slug'             => $campaign->slug,
            'description'      => $campaign->description,
            'banner_image_url' => $campaign->banner_image_url,
            'meta_image_url'   => $campaign->meta_image_url,
            'badge_label'      => $campaign->badge_label,
            'color_hex'        => $campaign->color_hex ?? '#FF7A00',
            'discount_type'    => $campaign->discount_type,
            'discount_value'   => $campaign->discount_value,
            'discount_label'   => $campaign->discount_label,
            'starts_at'        => $campaign->starts_at?->toIso8601String(),
            'ends_at'          => $campaign->ends_at?->toIso8601String(),
            'coupons_count'    => $campaign->coupons()->where('is_active', true)->count(),
            'seo'              => $campaign->seo,
        ];

        if ($full) {
            $data['body'] = $campaign->body;
            $data['coupons'] = $campaign->coupons()
                ->where('is_active', true)
                ->get()
                ->map(fn ($c) => [
                    'code'           => $c->code,
                    'discount_type'  => $c->discount_type,
                    'discount_value' => $c->discount_value,
                    'ends_at'        => $c->ends_at?->toIso8601String(),
                ])
                ->values();
        }

        return $data;
    }

    private function serializeNavigationItem(NavigationItem $item): array
    {
        return [
            'id' => $item->id,
            'label' => $item->label,
            'url' => $item->url,
            'icon' => $item->icon,
            'external' => str_starts_with($item->url, 'http://') || str_starts_with($item->url, 'https://'),
        ];
    }
}
