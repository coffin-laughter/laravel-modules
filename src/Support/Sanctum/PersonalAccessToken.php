<?php

declare(strict_types=1);

/**
 *  +-------------------------------------------------------------------------------------------
 *  | Coffin [ 花开不同赏，花落不同悲。欲问相思处，花开花落时。 ]
 *  +-------------------------------------------------------------------------------------------
 *  | This is not a free software, without any authorization is not allowed to use and spread.
 *  +-------------------------------------------------------------------------------------------
 *  | Copyright (c) 2006~2024 All rights reserved.
 *  +-------------------------------------------------------------------------------------------
 *  | @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
 *  +-------------------------------------------------------------------------------------------
 */

namespace Nwidart\Modules\Support\Sanctum;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\PersonalAccessToken as Sanctum;

class PersonalAccessToken extends Sanctum
{
    /**
     * The seconds to cache the token for.
     *
     */
    public static int $ttl = 3600;

    /**
     * Bootstrap the model and its traits.
     *
     * todo update cache
     *
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        static::updating(function (self $personalAccessToken) {
            // update cache last_use_at
        });

        static::deleting(function (self $personalAccessToken) {
            Cache::forget("personal-access-token:{$personalAccessToken->token}");
            //            Cache::forget("personal-access-token:{$personalAccessToken->id}:last_used_at");
            Cache::forget("personal-access-token:{$personalAccessToken->token}:tokenable");
        });
    }

    /**
     * Find the token instance matching the given token.
     *
     * @param string $token
     * @return static|null
     */
    public static function findToken($token): ?static
    {
        [$id, $token] = !str_contains($token, '|')
            ? [null, $token]
            : explode('|', $token, 2);
        $hashedToken = hash('sha256', $token);

        $cachedToken = Cache::remember(
            "personal-access-token:$hashedToken",
            config('sanctum.cache.ttl') ?? self::$ttl,
            function () use ($token) {
                return parent::findToken($token) ?? '_null_';
            }
        );

        if ($cachedToken === '_null_' || !hash_equals($cachedToken->token, $hashedToken)) {
            return null;
        }

        return $cachedToken;
    }

    /**
     * Get the tokenable model that the access token belongs to.
     *
     * @return Attribute
     *
     * help wanted: return type ain't compatible with base class
     *
     * @phpstan-ignore-next-line
     */
    public function tokenable(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => Cache::remember(
                "personal-access-token:{$attributes['token']}:tokenable",
                config('sanctum.cache.ttl') ?? self::$ttl,
                function () {
                    return parent::tokenable()->first();
                }
            )
        );
    }
}
