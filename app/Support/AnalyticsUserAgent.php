<?php

namespace App\Support;

class AnalyticsUserAgent
{
    public static function parse(?string $userAgent): array
    {
        $agent = strtolower((string) $userAgent);

        $browser = match (true) {
            str_contains($agent, 'edg/') || str_contains($agent, 'edge/') => 'Edge',
            str_contains($agent, 'opr/') || str_contains($agent, 'opera') => 'Opera',
            str_contains($agent, 'chrome/') && ! str_contains($agent, 'chromium') => 'Chrome',
            str_contains($agent, 'safari/') && ! str_contains($agent, 'chrome/') => 'Safari',
            str_contains($agent, 'firefox/') => 'Firefox',
            default => 'Other',
        };

        $os = match (true) {
            str_contains($agent, 'windows') => 'Windows',
            str_contains($agent, 'android') => 'Android',
            str_contains($agent, 'iphone') || str_contains($agent, 'ipad') || str_contains($agent, 'ios') => 'iOS',
            str_contains($agent, 'mac os') || str_contains($agent, 'macintosh') => 'macOS',
            str_contains($agent, 'linux') => 'Linux',
            default => 'Other',
        };

        $device = match (true) {
            str_contains($agent, 'bot') || str_contains($agent, 'crawl') || str_contains($agent, 'spider') => 'bot',
            str_contains($agent, 'ipad') || str_contains($agent, 'tablet') => 'tablet',
            str_contains($agent, 'mobi') || str_contains($agent, 'android') || str_contains($agent, 'iphone') => 'mobile',
            default => 'desktop',
        };

        return [
            'browser' => $browser,
            'os' => $os,
            'device_type' => $device,
        ];
    }
}
