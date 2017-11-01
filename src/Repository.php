<?php

namespace Katsana\Model\Concerns;

use InvalidArgumentException;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Database\Eloquent\Model;

class Repository
{
    /**
     * Eloquent instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $eloquent;

    /**
     * Registered cabinet collection.
     *
     * @var array
     */
    protected $collections = [];

    /**
     * Construct a new eloquent repository.
     *
     * @param \Illuminate\Database\Eloquent\Model $eloquent
     */
    public function __construct(Model $eloquent)
    {
        $this->eloquent = $eloquent;
        $this->memory = new self(new ArrayStore());
    }

    /**
     * Add caching service.
     *
     * @param string   $key
     * @param callable $callback
     */
    public function add(string $key, callable $callback): self
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

        return $this->memory->sear($key, function () use ($key) {
            return $this->collections[$key]($this->eloquent);
        });
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

        return $this;
    }
}
