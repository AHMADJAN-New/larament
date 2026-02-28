<?php

namespace Tests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create([
            'name' => 'amin',
            'username' => 'amin',
            'email' => 'amin@example.com',
            'password' => 'amin123',
            'role' => UserRole::Admin->value,
        ]));

        $this->withoutVite();
    }
}
