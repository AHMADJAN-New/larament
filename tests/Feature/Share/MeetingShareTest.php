<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Meeting;
use App\Models\ShareLink;
use App\Models\User;

use function Pest\Laravel\assertDatabaseCount;

it('creates a share link from authenticated route', function () {
    $meeting = Meeting::factory()->create();

    $response = $this->get(route('meetings.share.create', $meeting));

    $response->assertRedirect();

    assertDatabaseCount('share_links', 1);
});

it('denies share link generation for viewer role', function () {
    $viewer = User::factory()->asRole(UserRole::Viewer)->create();
    $meeting = Meeting::factory()->create();
    $this->actingAs($viewer);

    $this->get(route('meetings.share.create', $meeting))
        ->assertForbidden();
});

it('shows public share page without login', function () {
    $meeting = Meeting::factory()->create();
    $shareLink = ShareLink::factory()->for($meeting)->create([
        'expires_at' => now()->addDay(),
    ]);

    auth()->logout();

    $this->get(route('meetings.share.show', $shareLink->token))
        ->assertOk()
        ->assertSee('د مجلس شریکول')
        ->assertSee($meeting->title);
});

it('returns gone for expired share token', function () {
    $meeting = Meeting::factory()->create();
    $shareLink = ShareLink::factory()->for($meeting)->create([
        'expires_at' => now()->subMinute(),
    ]);

    auth()->logout();

    $this->get(route('meetings.share.show', $shareLink->token))
        ->assertStatus(410);
});
