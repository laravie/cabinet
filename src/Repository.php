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
        if (\method_exists($cache->getStore(), 'tags')) {
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
        return $this->share($key, $callback, 'forever');
    }

    /**
     * Register new persistent cache data.
     *
     * @param  string  $key
     * @param  callable  $callback
     *
     * @return mixed
     */
    public function rememberForever(string $key, callable $callback)
    {
        return $this->remember($key, $callback, 'forever');
    }

    /**
     * Register new persistent cache data.
     *
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|float|int  $ttl
     * @param  callable  $callback
     *
     * @return mixed
     */
    public function remember(string $key, $ttl, callable $callback)
    {
        $this->share($key, $callback, $ttl);

        return $this->get($key);
    }

    /**
     * Register new cache data.
     *
     * @param  string  $key
     * @param  callable  $callback
     * @param  \DateTimeInterface|\DateInterval|float|int|string|null  $ttl
     *
     * @return $this
     */
    public function share(string $key, callable $callback, $ttl = null)
    {
        $this->collections[$key] = Item::create($this->eloquent, $key, $callback, $ttl);

        return $this;
    }

    /**
     * Register new cache data.
     *
     * @param  string  $key
     * @param  callable  $callback
     * @param  \DateTimeInterface|\DateInterval|float|int|string|null  $ttl
     *
     * @return $this
     */
    public function put(string $key, callable $callback, $ttl = null)
    {
        return $this->share($key, $callback, $ttl ?? 'forever');
    }

    /**
     * Get cache data.
     *
     * @param  string  $key
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

        return $this->getFromStorage($key, $item->get('ttl'), $item->get('resolver'));
    }

    /**
     * Get fresh from storage.
     *
     * @param  string  $key
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function fresh(string $key)
    {
        return $this->forget($key)->get($key);
    }

    /**
     * Get item value from storage.
     *
     * @param  string  $key
     * @param  \DateTimeInterface|\DateInterval|float|int|string|null  $ttl
     * @param  callable  $callback
     *
     * @return mixed
     */
    protected function getFromStorage(string $key, $ttl, callable $callback)
    {
        if (\is_null($this->storage) || \is_null($ttl)) {
            return $this->getMemory()->remember($key, $ttl, $callback);
        }

        try {
            return $this->storage->remember($key, $ttl, $callback);
        } catch (Exception | Throwable $e) {
            $this->storage->forget($key);
        }

        return $this->storage->remember($key, $ttl, $callback);
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

        if (! \is_null($this->storage)) {
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

        if (! \is_null($this->storage)) {
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
            \sprintf('cabinet-%s:%s-%s', $eloquent->getConnectionName(), $eloquent->getTable(), $eloquent->getKey()),
            \sprintf('cabinet-%s-%s', $eloquent->getTable(), $eloquent->getKey()),
        ];
    }
}
