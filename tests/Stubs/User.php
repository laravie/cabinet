<?php

namespace Laravie\Cabinet\Tests\Stubs;

use Laravie\Cabinet\Cabinet;
use Laravie\Cabinet\Repository;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Cabinet;

    protected function onCabinet(Repository $cabinet)
    {
        $cabinet->register('friends', function ($user) {
            return ['Taylor', 'Mior Muhammad Zaki'];
        });
    }
}
