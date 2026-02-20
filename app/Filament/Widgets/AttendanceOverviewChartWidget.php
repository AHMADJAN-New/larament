<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\AttendanceStatus;
use App\Models\MeetingAttendee;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

final class AttendanceOverviewChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'حاضري (حاضر / غیر حاضر)';

    protected ?string $description = 'په وروستو ۶ میاشتو کې د حاضري لنډیز';

    protected ?string $maxHeight = '220px';

    protected string $color = 'success';

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $today = Carbon::today();
        $labels = [];
        $presentData = [];
        $absentData = [];

        for ($i = 5; $i >= 0; $i--) {
            $start = $today->copy()->subMonths($i)->startOfMonth();
            $end = $today->copy()->subMonths($i)->endOfMonth();
            $labels[] = \Morilog\Jalali\Jalalian::fromCarbon($start)->format('Y/m');

            $presentData[] = MeetingAttendee::query()
                ->whereHas('meeting', fn ($q) => $q->whereBetween('date', [$start, $end]))
                ->where('status', AttendanceStatus::Present)
                ->count();

            $absentData[] = MeetingAttendee::query()
                ->whereHas('meeting', fn ($q) => $q->whereBetween('date', [$start, $end]))
                ->where('status', AttendanceStatus::Absent)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'حاضر',
                    'data' => $presentData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'غیر حاضر',
                    'data' => $absentData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
