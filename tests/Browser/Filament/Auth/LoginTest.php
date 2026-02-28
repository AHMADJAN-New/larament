<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    auth()->logout();

    $this->user = User::factory()->create([
        'username' => 'demouser',
        'email' => 'demo@pestphp.com',
        'password' => 'password',
    ]);
});

test('an unauthenticated user can login', function () {
    visit('/admin/login')
        ->fill('form.data.username', $this->user->username)
        ->fill('form.data.password', 'password')
        ->submit()
        ->assertSee('Dashboard');

    $this->assertAuthenticated();
});
