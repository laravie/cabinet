<?php

namespace Laravie\Cabinet;

use Exception;
use Throwable;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Cache\Repository as CacheContract;

class Repository
{
    /**
     * Eloquent instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $eloquent;

    /**
     * Runtime cache repository.
     *
     * @var \Laravie\Cabinet\Contracts\Storage
     */
    protected $memory;

    /**
     * Persistent cache repository.
     *
     * @var \Laravie\Cabinet\Contracts\Storage
     */
    protected $storage;

    /**
     * Registered cabinet collection.
     *
     * @var array
     */
    protected $collections = [];

    /**
     * List of tags.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Construct a new eloquent repository.
     *
     * @param \Illuminate\Database\Eloquent\Model  $eloquent
     */
    public function __construct(Model $eloquent)
    {
        $this->eloquent = $eloquent;
        $this->tags = $this->resolveTags($eloquent);
    }

    /**
     * Set persistent cache repository.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     *
     * @return $this
     */
    public function setStorage(CacheContract $cache)
    {
        if (method_exists($cache->getStore(), 'tags')) {
            $this->storage = new Storage\Persistent($cache, $this->tags);
        }

        return $this;
    }

    /**
     * Register new persistent cache data.
     *
     * @param  string  $key
     * @param  callable  $callback
     *
     * @return $this
     */
    public function forever(string $key, callable $callback)
    {
        return $this->register($key, $callback, 'forever');
    }

    /**
     * Register new persistent cache data.
     *
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|float|int  $minutes
     * @param  callable  $callback
     *
     * @return $this
     */
    public function remember(string $key, $minutes, callable $callback)
    {
        return $this->register($key, $callback, $minutes);
    }

    /**
     * Register new in-memory cache data.
     *
     * @param  string  $key
     * @param  callable  $callback
     * @param  \DateTimeInterface|\DateInterval|float|int|string|null  $duration
     *
     * @return $this
     */
    public function register(string $key, callable $callback, $duration = null)
    {
        $this->collections[$key] = Item::create($this->eloquent, $key, $callback, $duration);

        return $this;
    }

    /**
     * Get cache data.
     *
     * @param  string $key
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function get(string $key)
    {
        $item = $this->collections[$key] ?? null;

        if (! $item instanceof Item) {
            throw new InvalidArgumentException("Requested [{$key}] is not registered!");
        }

        return $this->getFromStorage($key, $item->get('duration'), $item->get('resolver'));
    }

    /**
     * Get item value from storage.
     *
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|float|int|string|null  $duration
     * @param  callable  $callback
     *
     * @return mixed
     */
    protected function getFromStorage(string $key, $duration, callable $callback)
    {
        if (is_null($this->storage) || is_null($duration)) {
            return $this->getMemory()->remember($key, $duration, $callback);
        }

        try {
            return $this->storage->remember($key, $duration, $callback);
        } catch (Exception | Throwable $e) {
            $this->storage->forget($key);
        }

        return $this->storage->remember($key, $duration, $callback);
    }

    /**
     * Forget cache by key.
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function forget(string $key)
    {
        $this->getMemory()->forget($key);

        if (! is_null($this->storage)) {
            $this->storage->forget($key);
        }

        return $this;
    }

    /**
     * Get memory instance.
     *
     * @return \Laravie\Cabinet\Storage\Runtime
     */
    protected function getMemory(): Storage\Runtime
    {
        if (! isset($this->memory)) {
            $this->memory = new Storage\Runtime($this->tags);
        }

        return $this->memory;
    }

    /**
     * Flush all keys.
     *
     * @return $this
     */
    public function flush()
    {
        $this->getMemory()->flush();

        if (! is_null($this->storage)) {
            $this->storage->flush();
        }

        return $this;
    }

    /**
     * Resolve tags for model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $eloquent
     *
     * @return array
     */
    private function resolveTags(Model $eloquent): array
    {
        return [
            sprintf('cabinet-%s:%s-%s', $eloquent->getConnectionName(), $eloquent->getTable(), $eloquent->getKey()),
            sprintf('cabinet-%s-%s', $eloquent->getTable(), $eloquent->getKey()),
        ];
    }
}
