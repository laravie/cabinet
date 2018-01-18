<?php

namespace Laravie\Cabinet\Storage;

use Closure;
use Illuminate\Cache\TaggedCache;
use Laravie\Cabinet\Contracts\Storage;

class Persistent implements Storage
{
    protected $cache;

    protected $tags = [];

    public function __construct(TaggedCache $cache, array $tags)
    {
        $this->cache = $cache;
        $this->tags = $tags;
    }

    /**
     * Get an item from the cache, or store the default value.
     *
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|float|int|string  $duration
     * @param  \Closure  $callback
     *
     * @return mixed
     */
    public function remember(string $key, $duration, Closure $callback)
    {
        if ($duration === 'forever') {
            return $this->cache->rememberForever($key, $callback);
        }

        return $this->cache->remember($key, $duration, $callback);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function forget(string $key): bool
    {
        return $this->cache->forget($key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return void
     */
    public function flush(): void
    {
        $this->cache->flush();
    }
}
