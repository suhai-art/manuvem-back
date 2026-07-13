<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_has_expected_fillable_attributes(): void
    {
        $user = new User();

        $this->assertEquals([
            'name',
            'email',
            'password',
        ], $user->getFillable());
    }

    public function test_user_has_expected_hidden_attributes(): void
    {
        $user = new User();

        $this->assertEquals([
            'password',
            'remember_token',
        ], $user->getHidden());
    }

    public function test_password_is_hashed_cast(): void
    {
        $user = new User();

        $this->assertEquals(
            'hashed',
            $user->getCasts()['password']
        );
    }

    public function test_email_verified_at_is_datetime_cast(): void
    {
        $user = new User();

        $this->assertEquals(
            'datetime',
            $user->getCasts()['email_verified_at']
        );
    }
}
