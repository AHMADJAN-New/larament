<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\TaskStatus;
use App\Models\Meeting;
use App\Models\MeetingTask;

final class MeetingShareMessageService
{
    public function build(Meeting $meeting, string $variant, string $shareUrl): string
    {
        return match ($variant) {
            'detailed' => $this->detailed($meeting, $shareUrl),
            'tasks_only' => $this->tasksOnly($meeting, $shareUrl),
            'absent_only' => $this->absentOnly($meeting, $shareUrl),
            'full' => $this->full($meeting, $shareUrl),
            default => $this->short($meeting, $shareUrl),
        };
    }

    public function short(Meeting $meeting, string $shareUrl): string
    {
        return implode(PHP_EOL, [
            'د مجلس لنډیز',
            'عنوان: '.$meeting->title,
            'نېټه: '.$meeting->date?->format('Y-m-d'),
            'ځای: '.($meeting->location ?? '-'),
            'لینک: '.$shareUrl,
        ]);
    }

    public function detailed(Meeting $meeting, string $shareUrl): string
    {
        $openTasksCount = $meeting->tasks()->where('status', '!=', TaskStatus::Done->value)->count();

        return implode(PHP_EOL, [
            'د مجلس تفصیلي لنډیز',
            'شمېره: '.$meeting->meeting_no,
            'عنوان: '.$meeting->title,
            'نېټه: '.$meeting->date?->format('Y-m-d'),
            'وخت: '.(($meeting->time ?? '-')),
            'ګډونوال: '.$meeting->attendees->count(),
            'نا بشپړ کارونه: '.$openTasksCount,
            'پریکړې: '.($meeting->decisions_text ?? '-'),
            'لینک: '.$shareUrl,
        ]);
    }

    public function tasksOnly(Meeting $meeting, string $shareUrl): string
    {
        $tasks = $meeting->tasks
            ->map(function (MeetingTask $task): string {
                $dueDate = $task->due_date?->format('Y-m-d') ?? '-';

                return '- '.$task->title.' | مسؤل: '.($task->owner ?? '-').' | نېټه: '.$dueDate;
            })
            ->values()
            ->all();

        return implode(PHP_EOL, [
            'د مجلس کارونه',
            ...$tasks,
            'لینک: '.$shareUrl,
        ]);
    }

    public function absentOnly(Meeting $meeting, string $shareUrl): string
    {
        $absent = $meeting->attendees()
            ->where('status', AttendanceStatus::Absent->value)
            ->get()
            ->map(function ($attendee): string {
                $reason = $attendee->reason ?: '-';

                return '- '.$attendee->name.' | علت: '.$reason;
            })
            ->values()
            ->all();

        return implode(PHP_EOL, [
            'غیر حاضر غړي',
            ...$absent,
            'لینک: '.$shareUrl,
        ]);
    }

    public function full(Meeting $meeting, string $shareUrl): string
    {
        return implode(PHP_EOL.PHP_EOL, [
            $this->detailed($meeting, $shareUrl),
            'تعقيب:'.PHP_EOL.($meeting->followup_text ?? '-'),
            'کارونه:'.PHP_EOL.$this->tasksOnly($meeting, $shareUrl),
            'غیر حاضر:'.PHP_EOL.$this->absentOnly($meeting, $shareUrl),
        ]);
    }
}
