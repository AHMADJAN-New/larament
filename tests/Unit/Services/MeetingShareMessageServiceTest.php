<?php

declare(strict_types=1);

use App\Enums\AttendanceStatus;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\MeetingTask;
use App\Services\MeetingShareMessageService;

it('builds tasks-only WhatsApp text', function () {
    $meeting = Meeting::factory()->create([
        'title' => 'د میاشتې مجلس',
    ]);

    MeetingTask::factory()->for($meeting)->create([
        'title' => 'اقدامي پلان',
        'owner' => 'حسن',
    ]);

    $message = app(MeetingShareMessageService::class)->build(
        meeting: $meeting->load('tasks'),
        variant: 'tasks_only',
        shareUrl: 'https://example.test/share/meeting/token',
    );

    expect($message)->toContain('د مجلس کارونه');
    expect($message)->toContain('اقدامي پلان');
    expect($message)->toContain('https://example.test/share/meeting/token');
});

it('builds absent-only WhatsApp text', function () {
    $meeting = Meeting::factory()->create();

    MeetingAttendee::factory()->for($meeting)->create([
        'name' => 'رحمت الله',
        'status' => AttendanceStatus::Absent->value,
        'reason' => 'رخصت',
    ]);

    $message = app(MeetingShareMessageService::class)->build(
        meeting: $meeting,
        variant: 'absent_only',
        shareUrl: 'https://example.test/share/meeting/token',
    );

    expect($message)->toContain('غیر حاضر غړي');
    expect($message)->toContain('رحمت الله');
    expect($message)->toContain('رخصت');
});
