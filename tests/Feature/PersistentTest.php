<?php

namespace Laravie\Cabinet\Tests\Feature;

use Laravie\Cabinet\Tests\TestCase;
use Laravie\Cabinet\Tests\Stubs\User;

class PersistentTest extends TestCase
{
    /** @test */
    public function it_reused_cached_value_on_each_called()
    {
        $user = factory(User::class)->create();

        $lastRead = $user->cabinet('last_read');

        $this->assertSame($lastRead, $user->cabinet('last_read'));
    }

    /** @test */
    public function it_can_forget_known_value_and_retrieve_new_value()
    {
        $user = factory(User::class)->create();

        $lastRead = $user->cabinet('last_read');

        $user->cabinet()->forget('now')->forget('last_read');

        sleep(1);

        $this->assertNotSame($user->cabinet('last_read'), $lastRead);
    }

     /** @test */
    public function it_can_flush_known_value_and_retrieve_new_value()
    {
        $user = factory(User::class)->create();

        $lastRead = $user->cabinet('last_read');

        $user->cabinet()->flush();

        sleep(1);

        $this->assertNotSame($user->cabinet('last_read'), $lastRead);
    }

    /** @test */
    public function it_cant_access_unknown_cache_key()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Requested [enemies] is not registered!');

        $user = factory(User::class)->create();

        $user->cabinet('enemies');
    }

    /** @test */
    public function it_can_persist_between_request()
    {
        $user = factory(User::class)->create();

        $lastRead = $user->cabinet()->forget('last_read')->get('last_read');

        sleep(1);

        $different = User::find($user->getKey());

        $this->assertEquals($different->cabinet('last_read'), $lastRead);

        $tags = [
            'cabinet-testing:users-1',
            'cabinet-users-1',
        ];

        $this->assertEquals(resolve('cache.store')->tags($tags)->get('last_read'), $lastRead);
    }
}
