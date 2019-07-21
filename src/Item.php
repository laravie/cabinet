<?php

namespace Laravie\Cabinet;

use Illuminate\Support\Fluent;
use Illuminate\Database\Eloquent\Model;

class Item extends Fluent
{
    /**
     * Create a new cache item.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $eloquent
     * @param  string. $key
     * @param  callable  $callback
     * @param  \DateTimeInterface|\DateInterval|float|int|string|null  $ttl
     *
     * @return static
     */
    public static function create(Model $eloquent, string $key, callable $callback, $ttl = null): Item
    {
        return new static([
            'key' => $key,
            'resolver' => static::createCacheResolver($eloquent, $callback),
            'ttl' => $ttl,
            'content' => null,
        ]);
    }

    /**
     * Create cache resolver.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $eloquent
     * @param  callable  $callback
     *
     * @return callable
     */
    protected static function createCacheResolver(Model $model, callable $callback): callable
    {
        return static function () use ($model, $callback) {
            return $callback($model);
        };
    }
}
