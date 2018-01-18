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
     * @param  \DateTimeInterface|\DateInterval|float|int|string|null  $duration
     *
     * @return static
     */
    public static function create(Model $eloquent, string $key, callable $callback, $duration = null): Item
    {
        return new static([
            'key' => $key,
            'resolver' => function () use ($callback, $eloquent) {
                return $callback($eloquent);
            },
            'duration' => $duration,
            'content' => null,
        ]);
    }
}
