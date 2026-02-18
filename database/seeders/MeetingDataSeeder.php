<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\MeetingTask;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class MeetingDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();
        if (! $user) {
            return;
        }

        $meetings = $this->meetingsData($user->id);

        foreach ($meetings as $data) {
            $attendeesData = $data['attendees'];
            $tasksData = $data['tasks'];
            unset($data['attendees'], $data['tasks']);

            $meeting = Meeting::query()->create($data);

            foreach ($attendeesData as $att) {
                MeetingAttendee::query()->create([
                    'meeting_id' => $meeting->id,
                    'name' => $att['name'],
                    'status' => $att['status'],
                    'reason' => $att['reason'] ?? null,
                ]);
            }

            foreach ($tasksData as $task) {
                MeetingTask::query()->create([
                    'meeting_id' => $meeting->id,
                    'title' => $task['title'],
                    'owner' => $task['owner'],
                    'due_date' => $task['due_date'] ?? null,
                    'priority' => $task['priority'],
                    'status' => $task['status'],
                    'description' => $task['description'] ?? null,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'closed_at' => ($task['status'] ?? '') === TaskStatus::Done->value ? now() : null,
                ]);
            }
        }

        $this->seedTemplates($user->id);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function meetingsData(int $userId): array
    {
        $today = Carbon::today();
        $base = [
            'created_by' => $userId,
            'updated_by' => $userId,
            'is_confidential' => false,
        ];

        return [
            [
                ...$base,
                'title' => 'د ادارې اونیزه غونډه',
                'date' => $today->copy()->subDays(7),
                'time' => '09:00',
                'location' => 'د دفتر مرکزي خونہ، کابل',
                'agenda' => "۱. د تیرې اونۍ کارونه مرور\n۲. د نوي پروژې پرېکړه\n۳. بودیجې خبرې اترې\n۴. نورې خبرې",
                'notes' => 'غونډه په وخت سره پیل شوه. ټولو غړو خپل راپورونه وړاندې کړل. د پروژې لپاره لومړیتوبونه وټاکل شول.',
                'decisions_text' => "• د نوي پروژې لپاره مالي ملاتړ تصویب شو.\n• راتلونکې غونډه په ورځ د شنبې به وي.",
                'followup_text' => 'د پروژې تفصیلي پلان تر راتلونکې اونۍ پورې چمتو کړی شي.',
                'attendees' => [
                    ['name' => 'عبدالحمید', 'status' => AttendanceStatus::Present->value, 'reason' => null],
                    ['name' => 'زینب', 'status' => AttendanceStatus::Present->value, 'reason' => null],
                    ['name' => 'محمد رضا', 'status' => AttendanceStatus::Absent->value, 'reason' => 'ناروغ'],
                    ['name' => 'فاطمه', 'status' => AttendanceStatus::Present->value, 'reason' => null],
                ],
                'tasks' => [
                    [
                        'title' => 'د پروژې پلان لیکل',
                        'owner' => 'عبدالحمید',
                        'due_date' => $today->copy()->addDays(7),
                        'priority' => TaskPriority::High->value,
                        'status' => TaskStatus::InProgress->value,
                        'description' => 'د پروژې ټولې پښې او وخت جدول په پلان کې شامل کړئ.',
                    ],
                    [
                        'title' => 'د بودیجې جدول وړاندې کول',
                        'owner' => 'زینب',
                        'due_date' => $today->copy()->addDays(5),
                        'priority' => TaskPriority::Medium->value,
                        'status' => TaskStatus::Open->value,
                        'description' => 'د مالیې څانګې لخوا جدول چمتو او وړاندې شي.',
                    ],
                ],
            ],
            [
                ...$base,
                'title' => 'د پروژې مشورتي غونډه',
                'date' => $today->copy()->subDays(3),
                'time' => '14:00',
                'location' => 'مشال روغتون، کنفرانس خونہ',
                'agenda' => "۱. د روغتیا پروژې وضعیت\n۲. د سامانونو اړتیا\n۳. د ګمارنو خبرې\n۴. راتلونکي ګامونه",
                'notes' => 'د روغتیا پروژې په ښه توګه روانې دي. د سامانونو لپاره اضافي بودیجه غوښتل شوې.',
                'decisions_text' => "• د سامانونو لپاره اضافه بودیجه ومنل شوه.\n• دوه نوي ګمارنې تصویب شوې.",
                'followup_text' => 'د ګمارنو اعلان تر راتلونکې ورځې پورې خپور شي.',
                'attendees' => [
                    ['name' => 'ډاکټر سید احمد', 'status' => AttendanceStatus::Present->value, 'reason' => null],
                    ['name' => 'نرګس', 'status' => AttendanceStatus::Present->value, 'reason' => null],
                    ['name' => 'حسین', 'status' => AttendanceStatus::Present->value, 'reason' => null],
                ],
                'tasks' => [
                    [
                        'title' => 'د ګمارنو اعلان خپورول',
                        'owner' => 'نرګس',
                        'due_date' => $today->copy()->addDay(),
                        'priority' => TaskPriority::High->value,
                        'status' => TaskStatus::Done->value,
                        'description' => 'د دوه ځایونو لپاره د ګمارنو اعلان چاپ او آنلاین خپور شي.',
                    ],
                ],
            ],
            [
                ...$base,
                'title' => 'د زده کړو څانګې غونډه',
                'date' => $today->copy()->subDay(),
                'time' => '10:30',
                'location' => 'د ښوونځي دفتر، جلال آباد',
                'agenda' => "۱. د زده کړیالانو شمیر\n۲. د نصاب نوې کول\n۳. د ښوونکو روزنه\n۴. د امتحان نېټې",
                'notes' => 'د زده کړیالانو شمیر زیات شوی. د نصاب نوې کولو لپاره کمېټه وټاکل شوه.',
                'decisions_text' => "• د نصاب نوې کول د راتلونکې میاشتې څخه پیل کیږي.\n• د ښوونکو روزنه د دوو اونیو وروسته.",
                'followup_text' => 'د کمېټې لومړۍ غونډه د شنبې.',
                'attendees' => [
                    ['name' => 'استاد غلام نبی', 'status' => AttendanceStatus::Present->value, 'reason' => null],
                    ['name' => 'میرا', 'status' => AttendanceStatus::Present->value, 'reason' => null],
                    ['name' => 'کریم', 'status' => AttendanceStatus::Absent->value, 'reason' => 'سفر'],
                    ['name' => 'سمیرا', 'status' => AttendanceStatus::Present->value, 'reason' => null],
                ],
                'tasks' => [
                    [
                        'title' => 'د نصاب مسوده چمتو کول',
                        'owner' => 'استاد غلام نبی',
                        'due_date' => $today->copy()->addDays(14),
                        'priority' => TaskPriority::High->value,
                        'status' => TaskStatus::Open->value,
                        'description' => 'د نوې نصاب مسوده د کمېټې سره مرسته چمتو کړئ.',
                    ],
                    [
                        'title' => 'د روزنې نېټه ټاکل',
                        'owner' => 'میرا',
                        'due_date' => $today->copy()->addDays(3),
                        'priority' => TaskPriority::Medium->value,
                        'status' => TaskStatus::Open->value,
                        'description' => 'د ښوونکو د روزنې لپاره وخت او ځای وټاکئ.',
                    ],
                ],
            ],
            [
                ...$base,
                'title' => 'د امنیت او پلان غونډه',
                'date' => $today,
                'time' => '11:00',
                'location' => 'د دفتر خونہ شمیره ۳',
                'agenda' => "۱. د امنیت وضعیت\n۲. د راتلونکې میاشتې پلان\n۳. د تجهیزاتو ساتنه\n۴. نور",
                'notes' => null,
                'decisions_text' => null,
                'followup_text' => null,
                'attendees' => [
                    ['name' => 'عمر', 'status' => AttendanceStatus::Present->value, 'reason' => null],
                    ['name' => 'لیلا', 'status' => AttendanceStatus::Present->value, 'reason' => null],
                ],
                'tasks' => [
                    [
                        'title' => 'د امنیت راپور لیکل',
                        'owner' => 'عمر',
                        'due_date' => $today->copy()->addDays(2),
                        'priority' => TaskPriority::Medium->value,
                        'status' => TaskStatus::Open->value,
                        'description' => 'د میاشتې امنیت راپور لیکل او وړاندې کول.',
                    ],
                ],
            ],
        ];
    }

    private function seedTemplates(int $userId): void
    {
        $templates = [
            [
                'name' => 'د اونیزې غونډې کښتۍ',
                'meeting_title_default' => 'د ادارې اونیزه غونډه',
                'agenda_template' => "۱. د تیرې اونۍ کارونه مرور\n۲. د اوسنۍ اونۍ هدفونه\n۳. خنډونه او حل یې\n۴. نورې خبرې",
                'notes_template' => 'د غونډې یادښتونه او خبرې دلته ولیکئ.',
                'decisions_template' => "• پرېکړه ۱:\n• پرېکړه ۲:\n• پرېکړه ۳:",
                'followup_template' => "• راتلونکی ګام ۱:\n• راتلونکی ګام ۲:",
                'tasks_template' => [
                    ['title' => 'دلته د دندې سرلیک ولیکئ', 'owner' => '', 'priority' => 'medium'],
                ],
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'د پروژې غونډه',
                'meeting_title_default' => 'د پروژې مشورتي غونډه',
                'agenda_template' => "۱. د پروژې وضعیت\n۲. د بودیجې راپور\n۳. د وخت جدول\n۴. راتلونکي ګامونه",
                'notes_template' => 'د پروژې اړوند یادښتونه.',
                'decisions_template' => "• د پروژې پرېکړې:\n",
                'followup_template' => "• راتلونکي دندې:\n",
                'tasks_template' => [
                    ['title' => 'دندې ۱', 'owner' => '', 'priority' => 'high'],
                    ['title' => 'دندې ۲', 'owner' => '', 'priority' => 'medium'],
                ],
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
            [
                'name' => 'عمومي غونډه',
                'meeting_title_default' => 'عمومي غونډه',
                'agenda_template' => "۱. موضوع ۱\n۲. موضوع ۲\n۳. موضوع ۳\n۴. پوښتنې او وړاندیزونه",
                'notes_template' => 'د غونډې یادښتونه.',
                'decisions_template' => "• پرېکړې:\n",
                'followup_template' => "• راتلونکي کارونه:\n",
                'tasks_template' => [],
                'created_by' => $userId,
                'updated_by' => $userId,
            ],
        ];

        foreach ($templates as $data) {
            Template::query()->create($data);
        }
    }
}
