<?php

namespace Laravie\Cabinet\Tests\Stubs;

use Carbon\Carbon;
use Laravie\Cabinet\Cabinet;
use Laravie\Cabinet\Repository;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Cabinet;

    protected function onCabinet(Repository $cabinet)
    {
        $cabinet->setStorage(resolve('cache.store'))
            ->register('now', function ($user) {
                return Carbon::now('Asia/Kuala_Lumpur');
            })->forever('last_read', function ($user) use ($cabinet) {
                return $cabinet->get('now')->toDateTimeString();
            });
    }
}
