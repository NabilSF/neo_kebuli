<?php

namespace App\Helpers;

class CacheKey
{
    public static function userDashboard(int $userId): string
    {
        $version = \Illuminate\Support\Facades\Cache::get("user.{$userId}.dashboard.version", 2);
        return "user.{$userId}.dashboard.v{$version}";
    }

    public static function invalidateUserDashboard(int $userId): void
    {
        \Illuminate\Support\Facades\Cache::increment("user.{$userId}.dashboard.version");
    }

    public static function userApplicationsIndex(int $userId, array $params = []): string
    {
        $version = \Illuminate\Support\Facades\Cache::get("user.{$userId}.applications.index.version", 1);
        $hash = md5(json_encode($params));
        return "user.{$userId}.applications.index.v{$version}.{$hash}";
    }

    public static function invalidateUserApplicationsIndex(int $userId): void
    {
        \Illuminate\Support\Facades\Cache::increment("user.{$userId}.applications.index.version");
    }

    public static function userApplicationShow(int $applicationId): string
    {
        $version = \Illuminate\Support\Facades\Cache::get("user.application.{$applicationId}.show.version", 1);
        return "user.application.{$applicationId}.show.v{$version}";
    }

    public static function invalidateUserApplicationShow(int $applicationId): void
    {
        \Illuminate\Support\Facades\Cache::increment("user.application.{$applicationId}.show.version");
    }

    public static function userNotifications(int $userId, int $page = 1): string
    {
        $version = \Illuminate\Support\Facades\Cache::get("user.{$userId}.notifications.version", 1);
        return "user.{$userId}.notifications.v{$version}.page{$page}";
    }

    public static function invalidateUserNotifications(int $userId): void
    {
        \Illuminate\Support\Facades\Cache::increment("user.{$userId}.notifications.version");
    }

    public static function userProfile(int $userId): string
    {
        $version = \Illuminate\Support\Facades\Cache::get("user.{$userId}.profile.version", 1);
        return "user.{$userId}.profile.v{$version}";
    }

    public static function invalidateUserProfile(int $userId): void
    {
        \Illuminate\Support\Facades\Cache::increment("user.{$userId}.profile.version");
    }

    public static function analyticsDashboard(string $type, ?int $limit = null): string
    {
        $version = \Illuminate\Support\Facades\Cache::get("analytics.dashboard.{$type}.version", 1);
        $key = "analytics.dashboard.{$type}.v{$version}";
        if ($limit !== null) {
            $key .= ".limit{$limit}";
        }
        return $key;
    }

    public static function invalidateAnalyticsDashboard(string $type): void
    {
        \Illuminate\Support\Facades\Cache::increment("analytics.dashboard.{$type}.version");
    }

    public static function kbliCategories(int $page, int $perPage): string
    {
        $version = \Illuminate\Support\Facades\Cache::get("kbli.categories.version", 1);
        return "kbli.categories.v{$version}.p{$page}.l{$perPage}";
    }

    public static function invalidateKbliCategories(): void
    {
        \Illuminate\Support\Facades\Cache::increment("kbli.categories.version");
    }
}
