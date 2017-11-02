<?php

namespace Laravie\Cabinet\Tests\Feature;

use Laravie\Cabinet\Tests\TestCase;
use Laravie\Cabinet\Tests\Stubs\User;

class UserTest extends TestCase
{
    /** @test */
    public function it_has_registered_caches()
    {
        $user = factory(User::class)->create();

        $this->assertSame(['Taylor', 'Mior Muhammad Zaki'], $user->cabinet('friends'));
    }
}
