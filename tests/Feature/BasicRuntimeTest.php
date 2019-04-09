<?php

namespace Laravie\Cabinet\Tests\Feature;

use Laravie\Cabinet\Tests\TestCase;
use Laravie\Cabinet\Tests\Stubs\User;

class BasicRuntimeTest extends TestCase
{
    /** @test */
    public function it_has_registered_caches()
    {
        $user = factory(User::class)->create();

        $user->cabinet()
            ->register('friends', function ($user) {
                return ['Taylor', 'Mior Muhammad Zaki'];
            });

        $this->assertSame(['Taylor', 'Mior Muhammad Zaki'], $user->cabinet('friends'));
        $this->assertSame(['Taylor', 'Mior Muhammad Zaki'], $user->cabinet()->get('friends'));
    }

    /** @test */
    public function it_reused_cached_value_on_each_called()
    {
        $user = factory(User::class)->create();

        $now = $user->cabinet('now');

        $this->assertSame($now, $user->cabinet('now'));
    }

    /** @test */
    public function it_can_forget_known_value_and_retrieve_new_value()
    {
        $user = factory(User::class)->create();

        $now = $user->cabinet('now');

        $user->cabinet()->forget('now');

        $this->assertNotSame($user->cabinet('now'), $now);
    }

     /** @test */
    public function it_can_flush_known_value_and_retrieve_new_value()
    {
        $user = factory(User::class)->create();

        $now = $user->cabinet('now');

        $user->cabinet()->flush();

        $this->assertNotSame($user->cabinet('now'), $now);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Requested [enemies] is not registered!
     */
    public function it_cant_access_unknown_cache_key()
    {
        $user = factory(User::class)->create();

        $user->cabinet('enemies');
    }
}
