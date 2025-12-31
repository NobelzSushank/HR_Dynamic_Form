<?php

namespace Modules\Core\Traits;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

trait Cacheable
{
    protected int $cacheTTl = 30 * 24 * 60 * 60;
    protected bool $isCached = true;

    public function storeCache(string $key, string $hash, callable $callback): object
    {
        try {
            if (!$this->isCached) {
                return $callback();
            }
            $cached = Cache::tags([$key, $hash])->rememberForever("{$key}.{$hash}", function () use ($callback) {
                return $callback();
            });
        } catch (Exception $exception) {
            throw $exception;
        }

        return $cached;
    }

    public function storeTtlCache(
        string $key,
        string $hash,
        callable $callback,
        ?int $ttl = null
    ): object {
        try {
            if (!$this->isCached) {
                return $callback();
            }
            $ttl = $ttl ?? $this->cacheTTl;

            $cached = Cache::tags([$key, $hash])->remember($key, $ttl, function () use ($callback) {
                return $callback();
            });
        } catch (Exception $exception) {
            throw $exception;
        }

        return $cached;
    }

    public function storeTtlCacheByTags(
        string $key,
        array $tags,
        callable $callback,
        ?int $ttl = null
    ): mixed {

        try {
            if (!$this->isCached) {
                return $callback();
            }
            $ttl = $ttl ?? $this->cacheTTl;

            $cached = Cache::tags($tags)->remember($key, $ttl, function () use ($callback) {
                return $callback();
            });
        } catch (Exception $exception) {
            throw $exception;
        }

        return $cached;
    }

    public function storeTagTtlCache(
        string $key,
        string $hash,
        callable $callback,
        ?int $ttl = null
    ): mixed {
        try {
            $ttl = $ttl ?? $this->cacheTTl;
            $cacheKey = "{$key}_{$hash}";

            $expireTime = Redis::ttl($cacheKey);
            $getCache = Redis::get($cacheKey);
            if (!$getCache || ($getCache && $expireTime == -2)) {
                $data = $callback();

                // If ttl is 0 set cache ttl to remember forever.
                if ($ttl == 0) {
                    Redis::set($cacheKey, json_encode($data));
                } else {
                    Redis::set($cacheKey, json_encode($data), "ex", $ttl);
                }
                $getCache = Redis::get($cacheKey);
            }

            $getCached = json_decode($getCache);
        } catch (Exception $exception) {
            throw $exception;
        }

        return $getCached;
    }

    public function storeTagCacheForever(
        string $key,
        string $hash,
        callable $callback
    ): mixed {
        try {
            $getCached = $this->storeTagTtlCache(
                key: $key,
                hash: $hash,
                callback: $callback,
                ttl: 0
            );
        } catch (Exception $exception) {
            throw $exception;
        }

        return $getCached;
    }

    public function flushTtlTagCache(string $key = "*"): void
    {
        try {
            $cacheName = $key == "*" ? $key : "{$key}*";
            $cacheKey = Redis::keys($cacheName);

            if (count($cacheKey) > 0) {
                Redis::del($cacheKey);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function flushTags(string $key): void
    {
        try {
            Cache::tags([$key])->flush();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getCacheHash(mixed ...$hash): string
    {
        $hash = json_encode($hash);
        return $hash;
    }

    public function checkCacheByTags(string $cacheKey, array $tags): bool
    {
        return Cache::tags($tags)->has($cacheKey);
    }

    public function getCacheByTags(string $cacheKey, array $tags): mixed
    {
        return Cache::tags($tags)->get($cacheKey);
    }
}