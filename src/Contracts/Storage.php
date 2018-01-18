<?php

namespace Laravie\Cabinet\Contracts;

use Closure;

interface Storage
{
    /**
     * Get an item from the cache, or store the default value.
     *
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|float|string|int  $duration
     * @param  \Closure  $callback
     *
     * @return mixed
     */
    public function remember(string $key, $duration, Closure $callback);

    /**
     * Get an item from the cache, or store the default value.
     *
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|float|int  $minutes
     * @param  \Closure  $callback
     *
     * @return mixed
     */
    public function forget(string $key): bool;

    /**
     * Remove all items from the cache.
     *
     * @return void
     */
    public function flush(): void;
}
