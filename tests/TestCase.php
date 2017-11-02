<?php

namespace Laravie\Cabinet\Tests;

use Orchestra\Testbench\TestCase as Testbench;

class TestCase extends Testbench
{
    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/factories');

        $this->loadLaravelMigrations('testing');
    }
}
