<?php

namespace Laravie\Cabinet;

trait Cabinet
{
    /**
     * Cabinet repository.
     *
     * @var \Laravie\Cabinet\Repository
     */
    protected $cabinet;

    /**
     * Get cabinet repository.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     *
     * @return \Laravie\Cabinet\Repository|mixed
     */
    public function cabinet(string $key = null, $default = null)
    {
        if (! isset($this->cabinet)) {
            $this->cabinet = new Repository($this);

            if (method_exists($this, 'bootCabinet')) {
                $this->bootCabinet($this->cabinet);
            }
        }

        if (! is_null($key)) {
            return $this->cabinet->get($key, $default);
        }

        return $this->cabinet;
    }

    /**
     * Boot cabinet.
     *
     * @param  \Laravie\Cabinet\Repository  $cabinet
     *
     * @return void
     */
    abstract protected function bootCabinet(Repository $cabinet);
}
