<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Meeting;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

final class MeetingsPerMonthChartWidget extends ChartWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'غونډې په میاشت کې';

    protected ?string $description = 'په وروستو ۶ میاشتو کې د غونډو شمېر';

    protected ?string $maxHeight = '220px';

    protected string $color = 'primary';

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $today = Carbon::today();
        $labels = [];
        $values = [];

        for ($i = 5; $i >= 0; $i--) {
            $d = $today->copy()->subMonths($i);
            $labels[] = \Morilog\Jalali\Jalalian::fromCarbon($d)->format('Y/m');
            $values[] = Meeting::query()
                ->whereYear('date', $d->year)
                ->whereMonth('date', $d->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'غونډې',
                    'data' => $values,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.6)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 1,
                    'fill' => true,
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
