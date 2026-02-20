<?php

declare(strict_types=1);

use App\Filament\Resources\Attendees\Pages\ListAttendees;
use App\Models\Attendee;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

it('can render the list page', function () {
    livewire(ListAttendees::class)
        ->assertOk();
});

it('can open create in slide over', function () {
    livewire(ListAttendees::class)
        ->mountAction('create')
        ->assertOk();
});

it('can open view in slide over', function () {
    $attendee = Attendee::factory()->create();

    livewire(ListAttendees::class)
        ->loadTable()
        ->mountTableAction('view', $attendee->getKey())
        ->assertOk();
});

it('can open edit in slide over', function () {
    $attendee = Attendee::factory()->create();

    livewire(ListAttendees::class)
        ->loadTable()
        ->mountTableAction('edit', $attendee->getKey())
        ->assertOk();
});

it('can create an attendee', function () {
    $data = [
        'name' => 'عبدالحمید',
        'user_id' => null,
    ];

    livewire(ListAttendees::class)
        ->callAction('create', $data)
        ->assertNotified();

    assertDatabaseHas(Attendee::class, [
        'name' => $data['name'],
        'user_id' => null,
    ]);
});

it('can update an attendee', function () {
    $attendee = Attendee::factory()->create(['name' => 'زینب']);
    $newName = 'زینب احمد';

    livewire(ListAttendees::class)
        ->loadTable()
        ->callAction(TestAction::make('edit')->table($attendee), ['name' => $newName])
        ->assertNotified();

    assertDatabaseHas(Attendee::class, [
        'id' => $attendee->getKey(),
        'name' => $newName,
    ]);
});

it('can delete an attendee', function () {
    $attendee = Attendee::factory()->create();

    livewire(ListAttendees::class)
        ->loadTable()
        ->callAction(TestAction::make('delete')->table($attendee))
        ->assertNotified();

    assertDatabaseMissing(Attendee::class, [
        'id' => $attendee->getKey(),
    ]);
});

it('validates required name when creating', function () {
    livewire(ListAttendees::class)
        ->callAction('create', ['name' => null])
        ->assertHasFormErrors(['name' => 'required'])
        ->assertNotNotified();
});
