<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUsername(): void
    {
        $user = $this->getUser();
        $this->assertNull($user->getUsername());

        $user->setUsername('jan');
        $this->assertSame('jan', $user->getUsername());
    }

    /**
     * @return User
     */
    protected function getUser(): User
    {
        return new User();
    }
}
