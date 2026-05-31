<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\AnalyticsPageView;
use App\Models\AnalyticsVisitor;
use App\Support\AnalyticsUserAgent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnalyticsController extends Controller
{
    public function pageView(Request $request): JsonResponse
    {
        $data = $request->validate([
            'visitor_id' => ['nullable', 'string', 'max:80'],
            'session_id' => ['nullable', 'string', 'max:100'],
            'url' => ['nullable', 'string', 'max:2048'],
            'path' => ['required', 'string', 'max:2048'],
            'title' => ['nullable', 'string', 'max:512'],
            'locale' => ['nullable', 'string', 'max:10'],
            'referrer' => ['nullable', 'string', 'max:2048'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_term' => ['nullable', 'string', 'max:255'],
            'utm_content' => ['nullable', 'string', 'max:255'],
            'screen_width' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'screen_height' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'viewport_width' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'viewport_height' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'page_loaded_ms' => ['nullable', 'integer', 'min:0', 'max:600000'],
            'time_on_page_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'language' => ['nullable', 'string', 'max:50'],
            'timezone' => ['nullable', 'string', 'max:100'],
        ]);

        if ($this->isIgnoredPath($data['path']) || $this->isBot($request)) {
            return response()->json(['ok' => true]);
        }

        $now = now();
        $visitorId = $data['visitor_id'] ?? (string) Str::uuid();
        $sessionId = $data['session_id'] ?? null;
        $userAgent = $request->userAgent();
        $agent = AnalyticsUserAgent::parse($userAgent);
        $ipHash = $this->hashIp($request->ip());
        $referrer = $data['referrer'] ?? null;
        $pathHash = $this->hashPath($data['path']);

        DB::transaction(function () use ($data, $visitorId, $sessionId, $userAgent, $agent, $ipHash, $referrer, $pathHash, $now): void {
            $visitor = AnalyticsVisitor::query()->firstOrCreate(
                ['visitor_id' => $visitorId],
                [
                    'first_ip_hash' => $ipHash,
                    'first_referrer' => $referrer,
                    'first_landing_path' => $data['path'],
                    'first_seen_at' => $now,
                    'user_agent' => $userAgent,
                    'locale' => $data['locale'] ?? null,
                    'language' => $data['language'] ?? null,
                    'timezone' => $data['timezone'] ?? null,
                    ...$agent,
                ]
            );

            $visitor->forceFill([
                'last_ip_hash' => $ipHash,
                'last_referrer' => $referrer,
                'last_path' => $data['path'],
                'last_seen_at' => $now,
                'locale' => $data['locale'] ?? $visitor->locale,
                'language' => $data['language'] ?? $visitor->language,
                'timezone' => $data['timezone'] ?? $visitor->timezone,
                'user_agent' => $userAgent ?: $visitor->user_agent,
                'browser' => $agent['browser'],
                'os' => $agent['os'],
                'device_type' => $agent['device_type'],
            ])->save();

            $visitor->increment('page_views_count');

            $sessionAlreadySeen = $sessionId
                ? AnalyticsPageView::query()
                    ->where('session_id', $sessionId)
                    ->where('analytics_visitor_id', $visitor->id)
                    ->exists()
                : false;

            if (! $sessionAlreadySeen) {
                $visitor->increment('visits_count');
            }

            AnalyticsPageView::query()->create([
                'analytics_visitor_id' => $visitor->id,
                'visitor_id' => $visitorId,
                'session_id' => $sessionId,
                'url' => $data['url'] ?? null,
                'path' => $data['path'],
                'path_hash' => $pathHash,
                'title' => $data['title'] ?? null,
                'locale' => $data['locale'] ?? null,
                'referrer' => $referrer,
                'referrer_domain' => $this->domainFromUrl($referrer),
                'utm_source' => $data['utm_source'] ?? null,
                'utm_medium' => $data['utm_medium'] ?? null,
                'utm_campaign' => $data['utm_campaign'] ?? null,
                'utm_term' => $data['utm_term'] ?? null,
                'utm_content' => $data['utm_content'] ?? null,
                'screen_width' => $data['screen_width'] ?? null,
                'screen_height' => $data['screen_height'] ?? null,
                'viewport_width' => $data['viewport_width'] ?? null,
                'viewport_height' => $data['viewport_height'] ?? null,
                'page_loaded_ms' => $data['page_loaded_ms'] ?? null,
                'time_on_page_seconds' => $data['time_on_page_seconds'] ?? null,
                'ip_hash' => $ipHash,
                'user_agent' => $userAgent,
                'viewed_at' => $now,
                ...$agent,
            ]);
        });

        return response()->json(['ok' => true, 'visitor_id' => $visitorId]);
    }

    public function event(Request $request): JsonResponse
    {
        $data = $request->validate([
            'visitor_id' => ['nullable', 'string', 'max:80'],
            'session_id' => ['nullable', 'string', 'max:100'],
            'event_name' => ['required', 'string', 'max:100'],
            'event_label' => ['nullable', 'string', 'max:512'],
            'url' => ['nullable', 'string', 'max:2048'],
            'path' => ['nullable', 'string', 'max:2048'],
            'payload' => ['nullable', 'array'],
        ]);

        if (($data['path'] ?? null) && $this->isIgnoredPath($data['path'])) {
            return response()->json(['ok' => true]);
        }

        $visitor = null;
        if (! empty($data['visitor_id'])) {
            $visitor = AnalyticsVisitor::query()->firstWhere('visitor_id', $data['visitor_id']);
        }

        $path = $data['path'] ?? null;
        $pathHash = $this->hashPath($path);
        $eventName = Str::of($data['event_name'])->lower()->snake()->limit(100, '')->toString();
        $payload = $data['payload'] ?? null;

        if ($eventName === 'page_leave') {
            $this->updatePageViewDuration(
                visitorId: $data['visitor_id'] ?? null,
                sessionId: $data['session_id'] ?? null,
                pathHash: $pathHash,
                seconds: (int) data_get($payload, 'time_on_page_seconds', 0),
            );
        }

        AnalyticsEvent::query()->create([
            'analytics_visitor_id' => $visitor?->id,
            'visitor_id' => $data['visitor_id'] ?? null,
            'session_id' => $data['session_id'] ?? null,
            'event_name' => $eventName,
            'event_label' => $data['event_label'] ?? null,
            'url' => $data['url'] ?? null,
            'path' => $path,
            'path_hash' => $pathHash,
            'payload' => $payload,
            'ip_hash' => $this->hashIp($request->ip()),
            'user_agent' => $request->userAgent(),
            'occurred_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    private function updatePageViewDuration(?string $visitorId, ?string $sessionId, ?string $pathHash, int $seconds): void
    {
        if (! $visitorId || ! $pathHash || $seconds < 2) {
            return;
        }

        $seconds = min($seconds, 86400);

        $query = AnalyticsPageView::query()
            ->where('visitor_id', $visitorId)
            ->where('path_hash', $pathHash);

        if ($sessionId) {
            $query->where('session_id', $sessionId);
        }

        $view = $query->latest('viewed_at')->first();

        if (! $view) {
            return;
        }

        $view->forceFill([
            'time_on_page_seconds' => max((int) ($view->time_on_page_seconds ?? 0), $seconds),
        ])->save();
    }

    private function hashPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $normalized = '/' . ltrim($path, '/');

        return hash('sha256', $normalized);
    }

    private function hashIp(?string $ip): ?string
    {
        if (! $ip) {
            return null;
        }

        return hash_hmac('sha256', $ip, (string) config('app.key'));
    }

    private function domainFromUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return $host ? strtolower($host) : null;
    }

    private function isIgnoredPath(?string $path): bool
    {
        $path = '/' . ltrim((string) $path, '/');

        return str_starts_with($path, '/admin')
            || str_starts_with($path, '/api')
            || str_starts_with($path, '/livewire')
            || str_starts_with($path, '/storage')
            || str_starts_with($path, '/images');
    }

    private function isBot(Request $request): bool
    {
        $agent = strtolower((string) $request->userAgent());

        return str_contains($agent, 'bot')
            || str_contains($agent, 'crawler')
            || str_contains($agent, 'spider')
            || str_contains($agent, 'preview')
            || str_contains($agent, 'facebookexternalhit')
            || str_contains($agent, 'telegrambot')
            || str_contains($agent, 'whatsapp');
    }
}
