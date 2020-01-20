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
            ->share('friends', function ($user) {
                return ['Taylor', 'Mior Muhammad Zaki'];
            });

        $user->cabinet()->put('foo', 'bar');

        $this->assertSame(['Taylor', 'Mior Muhammad Zaki'], $user->cabinet('friends'));
        $this->assertSame(['Taylor', 'Mior Muhammad Zaki'], $user->cabinet()->get('friends'));
        $this->assertSame('bar', $user->cabinet()->get('foo'));
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

        $this->assertSame($user->cabinet('now'), $now);

        $user->cabinet()->forget('now');

        $this->assertNotSame($user->cabinet('now'), $now);
    }

    /** @test */
    public function it_can_flush_known_value_and_retrieve_new_value()
    {
        $user = factory(User::class)->create();

        $now = $user->cabinet('now');

        $this->assertSame($user->cabinet('now'), $now);

        $user->cabinet()->flush();

        $this->assertNotSame($user->cabinet('now'), $now);
    }

    /** @test */
    public function it_can_get_fresh_from_known_value()
    {
        $user = factory(User::class)->create();

        $now = $user->cabinet('now');

        $this->assertSame($user->cabinet('now'), $now);

        $this->assertNotSame($user->cabinet()->fresh('now'), $now);
    }

    /** @test */
    public function it_cant_access_unknown_cache_key()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Requested [enemies] is not registered!');

        $user = factory(User::class)->create();

        $user->cabinet('enemies');
    }
}
