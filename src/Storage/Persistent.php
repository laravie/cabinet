<?php

namespace Laravie\Cabinet\Storage;

use Closure;
use Laravie\Cabinet\Contracts\Storage;
use Illuminate\Contracts\Cache\Repository as CacheContract;

class Persistent implements Storage
{
    /**
     * The tagged cache instance.
     *
     * @var \Illuminate\Cache\TaggedCache
     */
    protected $cache;

    /**
     * List of tags.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Construct a new storage.
     *
     * @param \Illuminate\Contracts\Cache\Repository  $cache
     * @param array  $tags
     */
    public function __construct(CacheContract $cache, array $tags)
    {
        $this->cache = $cache->tags($tags);
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
        if (\is_null($duration) || $duration === 'forever') {
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
