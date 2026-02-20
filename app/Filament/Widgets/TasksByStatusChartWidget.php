<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\TaskStatus;
use App\Models\MeetingTask;
use Filament\Widgets\ChartWidget;

final class TasksByStatusChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'کارونه د حالت له مخې';

    protected ?string $description = 'پرانیستې، روان، بشپړ او بند کارونه';

    protected ?string $maxHeight = '220px';

    protected string $color = 'warning';

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $counts = [
            TaskStatus::Open->label() => MeetingTask::query()->where('status', TaskStatus::Open)->count(),
            TaskStatus::InProgress->label() => MeetingTask::query()->where('status', TaskStatus::InProgress)->count(),
            TaskStatus::Done->label() => MeetingTask::query()->where('status', TaskStatus::Done)->count(),
            TaskStatus::Blocked->label() => MeetingTask::query()->where('status', TaskStatus::Blocked)->count(),
        ];

        return [
            'datasets' => [
                [
                    'label' => 'کارونه',
                    'data' => array_values($counts),
                    'backgroundColor' => [
                        'rgb(245, 158, 11)',
                        'rgb(14, 165, 233)',
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => array_keys($counts),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
