<?php

declare(strict_types=1);

use App\Filament\Resources\Meetings\Pages\CreateMeeting;
use App\Filament\Resources\Meetings\Pages\EditMeeting;
use App\Filament\Resources\Meetings\Pages\ListMeetings;
use App\Filament\Resources\Meetings\Pages\MeetingsCalendar;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\MeetingTask;

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

it('can render the edit page', function () {
    $meeting = Meeting::factory()->create();

    MeetingAttendee::factory()->create([
        'meeting_id' => $meeting->getKey(),
    ]);

    MeetingTask::factory()->create([
        'meeting_id' => $meeting->getKey(),
    ]);

    livewire(EditMeeting::class, [
        'record' => $meeting->getKey(),
    ])->assertOk();
});

it('can render meetings calendar page', function () {
    livewire(MeetingsCalendar::class)
        ->assertOk()
        ->assertSee('د مجلسونو کلینډر')
        ->assertSee('نن');
});

it('shows meetings for the selected month and year', function () {
    Meeting::factory()->create([
        'meeting_no' => 11,
        'title' => 'جنوري ناسته',
        'date' => '2026-01-15',
        'time' => '09:30:00',
        'location' => 'کابل',
    ]);

    Meeting::factory()->create([
        'meeting_no' => 12,
        'title' => 'فبروري ناسته',
        'date' => '2026-02-10',
        'time' => '10:00:00',
        'location' => 'هرات',
    ]);

    livewire(MeetingsCalendar::class)
        ->set('month', 1)
        ->set('year', 2026)
        ->assertSee('جنوري ناسته')
        ->assertDontSee('فبروري ناسته');
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
