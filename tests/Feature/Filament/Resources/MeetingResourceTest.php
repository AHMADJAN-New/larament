<?php

declare(strict_types=1);

use App\Filament\Resources\Meetings\Pages\CreateMeeting;
use App\Filament\Resources\Meetings\Pages\ListMeetings;
use App\Models\Meeting;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

it('can open the view dialog from the list page', function () {
    $meeting = Meeting::factory()->create([
        'title' => 'د مجلس ناسته',
        'date' => now(),
    ]);

    livewire(ListMeetings::class)
        ->loadTable()
        ->mountTableAction('view', $meeting->getKey())
        ->assertOk();
});

it('displays meeting data in the view dialog', function () {
    $meeting = Meeting::factory()->create([
        'title' => 'د مجلس ناسته',
        'date' => now(),
        'agenda' => 'اجنډا متن',
    ]);

    livewire(ListMeetings::class)
        ->loadTable()
        ->mountTableAction('view', $meeting->getKey())
        ->assertMountedActionModalSee('د مجلس ناسته')
        ->assertMountedActionModalSee('اجنډا متن');
});

it('list table has PDF and share row actions', function () {
    $meeting = Meeting::factory()->create();

    livewire(ListMeetings::class)
        ->loadTable()
        ->assertTableActionExists('decisions_pdf')
        ->assertTableActionExists('followup_pdf')
        ->assertTableActionExists('share');
});

it('can render the create page', function () {
    livewire(CreateMeeting::class)
        ->assertOk();
});

it('can create a meeting', function () {
    $data = [
        'title' => 'نوې ناسته',
        'date' => now()->toDateString(),
        'time' => '10:00',
        'location' => 'کابل',
    ];

    livewire(CreateMeeting::class)
        ->fillForm($data)
        ->call('create')
        ->assertNotified();

    assertDatabaseHas(Meeting::class, [
        'title' => $data['title'],
        'location' => $data['location'],
        'created_by' => auth()->id(),
    ]);
});

it('validates required fields when creating a meeting', function () {
    livewire(CreateMeeting::class)
        ->fillForm([
            'title' => null,
            'date' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'title' => 'required',
            'date' => 'required',
        ])
        ->assertNotNotified();
});
