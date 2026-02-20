<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\TaskStatus;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\MeetingTask;
use BackedEnum;

final class MeetingShareMessageService
{
    public function build(Meeting $meeting, string $variant): string
    {
        return match ($variant) {
            'detailed' => $this->detailed($meeting),
            'tasks_only' => $this->tasksOnly($meeting),
            'absent_only' => $this->absentOnly($meeting),
            'full' => $this->full($meeting),
            default => $this->short($meeting),
        };
    }

    public function short(Meeting $meeting): string
    {
        /** @var \Carbon\Carbon|null $date */
        $date = $meeting->date;

        $lines = [
            '*د مجلس لنډیز*',
            '',
            'عنوان: '.$meeting->title,
            'شمېره: '.$meeting->meeting_no,
            'نېټه: '.shamsi_date($date),
            'ځای: '.($meeting->location ?? '-'),
        ];

        return implode(PHP_EOL, $lines);
    }

    public function detailed(Meeting $meeting): string
    {
        $openTasksCount = $meeting->tasks()->where('status', '!=', TaskStatus::Done->value)->count();

        /** @var \Carbon\Carbon|null $date */
        $date = $meeting->date;

        $lines = [
            '*د مجلس تفصیلي لنډیز*',
            '',
            'شمېره: '.$meeting->meeting_no,
            'عنوان: '.$meeting->title,
            'نېټه: '.shamsi_date($date),
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

    public function tasksOnly(Meeting $meeting): string
    {
        $content = $this->tasksOnlyContent($meeting);
        $lines = [
            '*د مجلس کارونه*',
            '',
            $content,
        ];

        return implode(PHP_EOL, $lines);
    }

    public function absentOnly(Meeting $meeting): string
    {
        $content = $this->absentOnlyContent($meeting);
        $lines = [
            '*غیر حاضر غړي*',
            '',
            $content,
        ];

        return implode(PHP_EOL, $lines);
    }

    public function full(Meeting $meeting): string
    {
        /** @var \Carbon\Carbon|null $date */
        $date = $meeting->date;
        $openTasksCount = $meeting->tasks()->where('status', '!=', TaskStatus::Done->value)->count();

        $parts = [
            '*د مجلس بشپړ لنډیز*',
            '',
            'شمېره: '.$meeting->meeting_no,
            'عنوان: '.$meeting->title,
            'نېټه: '.shamsi_date($date),
            'وخت: '.($meeting->time ?? '-'),
            'ځای: '.($meeting->location ?? '-'),
            'ګډونوال: '.$meeting->attendees->count(),
            'نا بشپړ کارونه: '.$openTasksCount,
            '',
        ];

        if (filled($meeting->agenda)) {
            $parts[] = '*اجنډا:*';
            $parts[] = mb_trim($meeting->agenda);
            $parts[] = '';
        }

        if (filled($meeting->notes)) {
            $parts[] = '*نوټونه:*';
            $parts[] = mb_trim($meeting->notes);
            $parts[] = '';
        }

        $parts[] = '*پرېکړې:*';
        $parts[] = $meeting->decisions_text ? mb_trim($meeting->decisions_text) : '—';
        $parts[] = '';

        if (filled($meeting->followup_text)) {
            $parts[] = '*تعقيب:*';
            $parts[] = mb_trim($meeting->followup_text);
            $parts[] = '';
        }

        $attendeesContent = $this->fullAttendeesContent($meeting);
        if ($attendeesContent !== '') {
            $parts[] = '*د مجلس غړي:*';
            $parts[] = $attendeesContent;
            $parts[] = '';
        }

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

    private function fullAttendeesContent(Meeting $meeting): string
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, MeetingAttendee> $collection */
        $collection = $meeting->attendees;
        $lines = $collection
            ->map(function (MeetingAttendee $attendee): string {
                $status = $attendee->status;
                $rawValue = $status instanceof BackedEnum ? $status->value : (string) $status;
                $statusLabel = AttendanceStatus::tryFrom($rawValue)?->label() ?? $rawValue;

                return '• '.$attendee->display_name.' — '.$statusLabel.($attendee->reason ? ' (علت: '.$attendee->reason.')' : '');
            })
            ->values()
            ->all();

        return $lines === [] ? '—' : implode(PHP_EOL, $lines);
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
                $rawValue = $status instanceof BackedEnum ? $status->value : (string) $status;
                $statusLabel = TaskStatus::tryFrom($rawValue)?->label() ?? $rawValue;

                return '• '.$task->title.PHP_EOL.'  مسؤل: '.($task->owner ?? '-').' | نېټه: '.shamsi_date($dueDate).' | حالت: '.$statusLabel;
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
