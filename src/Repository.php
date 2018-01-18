<?php

namespace Laravie\Cabinet;

use InvalidArgumentException;
use Illuminate\Cache\ArrayStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Cache\Repository as CacheRepository;
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
     * In-memory cache repository.
     *
     * @var \Illuminate\Cache\Repository
     */
    protected $memory;

    /**
     * Persistent cache repository.
     *
     * @var \Illuminate\Cache\TaggedCache|null
     */
    protected $storage;

    /**
     * Registered cabinet collection.
     *
     * @var array
     */
    protected $collections = [];

    /**
     * Persistents collection.
     *
     * @var array
     */
    protected $remembers = [];

    /**
     * Construct a new eloquent repository.
     *
     * @param \Illuminate\Database\Eloquent\Model  $eloquent
     */
    public function __construct(Model $eloquent)
    {
        $this->eloquent = $eloquent;
        $this->memory = new CacheRepository(new ArrayStore());
    }

    /**
     * Set persistent cache repository.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     *
     * @return $this
     */
    public function setStorage(CacheContract $cache): self
    {
        $model = $this->eloquent;

        $tags = [
            sprintf('cabinet-%s:%s-%s', $model->getConnectionName(), $model->getTable(), $model->getKey()),
            sprintf('cabinet-%s-%s', $model->getTable(), $model->getKey())
        ];

        if (method_exists($cache->getStore(), 'tags')) {
            $this->storage = $cache->tags($tags);
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
    public function forever(string $key, callable $callback): self
    {
        $this->remembers[$key] = 'forever';

        return $this->register($key, $callback);
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
    public function remember(string $key, $minutes, callable $callback): self
    {
        $this->remembers[$key] = $minutes;

        return $this->register($key, $callback);
    }

    /**
     * Register new in-memory cache data.
     *
     * @param  string  $key
     * @param  callable  $callback
     *
     * @return $this
     */
    public function register(string $key, callable $callback): self
    {
        $this->collections[$key] = $callback;

        return $this;
    }

    /**
     * Get cache data.
     *
     * @param  string $key
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function get(string $key)
    {
        if (! array_key_exists($key, $this->collections)) {
            throw new InvalidArgumentException("Requested [{$key}] is not registered!");
        }

        $callback = $this->getCacheResolver($key);

        $duration = $this->remembers[$key] ?? null;

        if (! is_null($this->storage) && ! is_null($duration)) {
            if ($duration === 'forever') {
                return $this->storage->rememberForever($key, $callback);
            }

            return $this->storage->remember($key, $duration, $callback);
        }

        return $this->memory->sear($key, $callback);
    }

    /**
     * Forget cache by key.
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function forget(string $key): self
    {
        $this->memory->forget($key);

        if (! is_null($this->storage)) {
            $this->storage->forget($key);
        }

        return $this;
    }

    /**
     * Flush all keys.
     *
     * @return $this
     */
    public function flush(): self
    {
        $keys = array_keys($this->collections);

        foreach ($keys as $key) {
            $this->memory->forget($key);
        }

        if (! is_null($this->storage)) {
            $this->storage->flush();
        }

        return $this;
    }

    /**
     * Get cache resolver.
     *
     * @param  string  $key
     *
     * @return callable
     */
    protected function getCacheResolver(string $key): callable
    {
        return function () use ($key) {
            return $this->collections[$key]($this->eloquent);
        };
    }
}
