<?php

namespace App\Services\Axenita;

use Illuminate\Support\Facades\Cache;

class AxenitaSession
{
    private string $cacheKey;

    public function __construct(?string $userKey = null)
    {
        // If this is per-user, pass auth()->id() here.
        $this->cacheKey = 'axenita.session.' . ($userKey ?? 'default');
    }

    public function get(): array
    {
        return Cache::get($this->cacheKey, []);
    }

    public function put(array $data, int $ttlSeconds = 3600): void
    {
        Cache::put($this->cacheKey, $data, $ttlSeconds);
    }

    public function merge(array $data, int $ttlSeconds = 3600): void
    {
        $this->put(array_merge($this->get(), $data), $ttlSeconds);
    }

    public function clear(): void
    {
        Cache::forget($this->cacheKey);
    }
}
