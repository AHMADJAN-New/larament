<?php

declare(strict_types=1);

use App\Enums\AttendanceStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\AuditLog;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\MeetingTask;
use App\Models\User;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('auto-generates meeting numbers and stores audit logs', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $firstMeeting = Meeting::query()->create([
        'title' => 'لومړۍ ناسته',
        'date' => now()->toDateString(),
    ]);

    $secondMeeting = Meeting::query()->create([
        'title' => 'دوهمه ناسته',
        'date' => now()->addDay()->toDateString(),
    ]);

    expect($firstMeeting->meeting_no)->toBe(1);
    expect($secondMeeting->meeting_no)->toBe(2);
    expect($firstMeeting->created_by)->toBe($user->id);

    assertDatabaseHas(AuditLog::class, [
        'action' => 'create',
        'entity_type' => Meeting::class,
        'entity_id' => (string) $firstMeeting->id,
    ]);
});

it('continues auto-incrementing after an explicit meeting number', function () {
    Meeting::query()->create([
        'meeting_no' => 50,
        'title' => 'Manual Number Meeting',
        'date' => now()->toDateString(),
    ]);

    $nextMeeting = Meeting::query()->create([
        'title' => 'Auto Number Meeting',
        'date' => now()->addDay()->toDateString(),
    ]);

    expect($nextMeeting->meeting_no)->toBe(51);
});

it('stores meeting attendees and tasks', function () {
    $meeting = Meeting::factory()->create();

    MeetingAttendee::factory()->for($meeting)->create([
        'name' => 'حامد',
        'status' => AttendanceStatus::Present->value,
    ]);

    MeetingAttendee::factory()->for($meeting)->create([
        'name' => 'شریف',
        'status' => AttendanceStatus::Absent->value,
        'reason' => 'سفر',
    ]);

    MeetingTask::factory()->for($meeting)->create([
        'title' => 'د راپور ترتیب',
        'priority' => TaskPriority::High->value,
        'status' => TaskStatus::InProgress->value,
    ]);

    assertDatabaseCount('meeting_attendees', 2);
    assertDatabaseCount('meeting_tasks', 1);
    assertDatabaseHas(MeetingAttendee::class, [
        'meeting_id' => $meeting->id,
        'status' => AttendanceStatus::Absent->value,
    ]);
    assertDatabaseHas(MeetingTask::class, [
        'meeting_id' => $meeting->id,
        'priority' => TaskPriority::High->value,
    ]);
});

it('generates followup PDF for authenticated user', function () {
    $user = User::factory()->create();
    $meeting = Meeting::factory()->create([
        'title' => 'د مجلس راپور',
        'followup_text' => 'تعقيب متن',
    ]);
    $meeting->load(['attendees', 'tasks']);

    $response = $this->actingAs($user)->get(route('meetings.pdf.followup', $meeting));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
    $response->assertHeader('Content-Disposition', 'attachment; filename="meeting-'.$meeting->meeting_no.'-followup.pdf"');
});
