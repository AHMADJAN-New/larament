<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\TaskStatus;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
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
        /** @var \Carbon\Carbon|null $date */
        $date = $meeting->date;

        $lines = [
            '📋 *د مجلس لنډیز*',
            '',
            'عنوان: '.$meeting->title,
            'شمېره: '.$meeting->meeting_no,
            'نېټه: '.($date !== null ? $date->format('Y-m-d') : '-'),
            'ځای: '.($meeting->location ?? '-'),
        ];

        return implode(PHP_EOL, $lines);
    }

    public function detailed(Meeting $meeting, string $shareUrl): string
    {
        $openTasksCount = $meeting->tasks()->where('status', '!=', TaskStatus::Done->value)->count();

        /** @var \Carbon\Carbon|null $date */
        $date = $meeting->date;

        $lines = [
            '📋 *د مجلس تفصیلي لنډیز*',
            '',
            'شمېره: '.$meeting->meeting_no,
            'عنوان: '.$meeting->title,
            'نېټه: '.($date !== null ? $date->format('Y-m-d') : '-'),
            'وخت: '.($meeting->time ?? '-'),
            'ځای: '.($meeting->location ?? '-'),
            'ګډونوال: '.$meeting->attendees->count(),
            'نا بشپړ کارونه: '.$openTasksCount,
            '',
            '*پرېکړې:*',
            $meeting->decisions_text ? mb_trim($meeting->decisions_text) : '—',
        ];

        return implode(PHP_EOL, $lines);
    }

    public function tasksOnly(Meeting $meeting, string $shareUrl): string
    {
        $content = $this->tasksOnlyContent($meeting);
        $lines = [
            '✅ *د مجلس کارونه*',
            '',
            $content,
        ];

        return implode(PHP_EOL, $lines);
    }

    public function absentOnly(Meeting $meeting, string $shareUrl): string
    {
        $content = $this->absentOnlyContent($meeting);
        $lines = [
            '👥 *غیر حاضر غړي*',
            '',
            $content,
        ];

        return implode(PHP_EOL, $lines);
    }

    public function full(Meeting $meeting, string $shareUrl): string
    {
        /** @var \Carbon\Carbon|null $date */
        $date = $meeting->date;
        $openTasksCount = $meeting->tasks()->where('status', '!=', TaskStatus::Done->value)->count();

        $parts = [
            '📋 *د مجلس بشپړ لنډیز*',
            '',
            'شمېره: '.$meeting->meeting_no,
            'عنوان: '.$meeting->title,
            'نېټه: '.($date !== null ? $date->format('Y-m-d') : '-'),
            'وخت: '.($meeting->time ?? '-'),
            'ځای: '.($meeting->location ?? '-'),
            'ګډونوال: '.$meeting->attendees->count(),
            'نا بشپړ کارونه: '.$openTasksCount,
            '',
            '*پرېکړې:*',
            $meeting->decisions_text ? mb_trim($meeting->decisions_text) : '—',
        ];

        if (filled($meeting->followup_text)) {
            $parts[] = '';
            $parts[] = '*تعقيب:*';
            $parts[] = mb_trim($meeting->followup_text);
        }

        $parts[] = '';
        $parts[] = '*کارونه:*';
        $parts[] = $this->tasksOnlyContent($meeting);

        $absentContent = $this->absentOnlyContent($meeting);
        if ($absentContent !== '') {
            $parts[] = '';
            $parts[] = '*غیر حاضر غړي:*';
            $parts[] = $absentContent;
        }

        return implode(PHP_EOL, $parts);
    }

    private function tasksOnlyContent(Meeting $meeting): string
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, MeetingTask> $taskCollection */
        $taskCollection = $meeting->tasks;
        $tasks = $taskCollection
            ->map(function (MeetingTask $task): string {
                /** @var \Carbon\Carbon|null $dueDate */
                $dueDate = $task->due_date;
                $status = $task->status;
                $rawValue = $status instanceof \BackedEnum ? $status->value : (string) $status;
                $statusLabel = TaskStatus::tryFrom($rawValue)?->label() ?? $rawValue;

                return '• '.$task->title.PHP_EOL.'  مسؤل: '.($task->owner ?? '-').' | نېټه: '.($dueDate !== null ? $dueDate->format('Y-m-d') : '-').' | حالت: '.$statusLabel;
            })
            ->values()
            ->all();

        return $tasks === [] ? '—' : implode(PHP_EOL.PHP_EOL, $tasks);
    }

    private function absentOnlyContent(Meeting $meeting): string
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, MeetingAttendee> $absentCollection */
        $absentCollection = $meeting->attendees()
            ->where('status', AttendanceStatus::Absent->value)
            ->get();
        $absent = $absentCollection
            ->map(function (MeetingAttendee $attendee): string {
                $reason = $attendee->reason ?: '—';

                return '• '.$attendee->name.' — علت: '.$reason;
            })
            ->values()
            ->all();

        return $absent === [] ? '—' : implode(PHP_EOL, $absent);
    }
}
