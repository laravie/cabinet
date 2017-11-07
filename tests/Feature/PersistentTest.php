<?php

namespace Laravie\Cabinet\Tests\Feature;

use Laravie\Cabinet\Tests\TestCase;
use Laravie\Cabinet\Tests\Stubs\User;

class PersistentTest extends TestCase
{
    /** @test */
    public function it_can_persist_between_request()
    {
        $user = factory(User::class)->create();

        $lastRead = $user->cabinet()->forget('last_read')->get('last_read');

        sleep(2);

        $different = User::find($user->getKey());

        $this->assertEquals($user->cabinet('now')->toDateTimeString(), $lastRead);
        $this->assertNotEquals($different->cabinet('now')->toDateTimeString(), $lastRead);
        $this->assertEquals($different->cabinet('last_read'), $lastRead);

        $tags = [
            'cabinet-testing:users-1',
            'cabinet-users-1',
        ];

        $this->assertEquals(resolve('cache.store')->tags($tags)->get('last_read'), $lastRead);
    }
}
