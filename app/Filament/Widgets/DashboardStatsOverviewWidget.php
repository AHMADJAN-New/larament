<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\AttendanceStatus;
use App\Enums\TaskStatus;
use App\Filament\Resources\Meetings\MeetingResource;
use App\Filament\Resources\MeetingTasks\MeetingTaskResource;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\MeetingTask;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

final class DashboardStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'لنډیز';

    protected ?string $description = 'د مجلسونو، کارونو او حاضري احصائیې';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        $meetingsThisMonth = Meeting::query()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->count();

        $upcomingCount = Meeting::query()
            ->where('date', '>=', $today)
            ->orderBy('date')
            ->limit(30)
            ->count();

        $openTasksCount = MeetingTask::query()
            ->whereNot('status', TaskStatus::Done)
            ->count();

        $overdueCount = MeetingTask::query()
            ->where('due_date', '<', $today)
            ->whereNot('status', TaskStatus::Done)
            ->count();

        $monthAttendees = MeetingAttendee::query()
            ->whereHas('meeting', fn ($q) => $q->whereBetween('date', [$monthStart, $monthEnd]))
            ->count();

        $monthPresent = MeetingAttendee::query()
            ->whereHas('meeting', fn ($q) => $q->whereBetween('date', [$monthStart, $monthEnd]))
            ->where('status', AttendanceStatus::Present)
            ->count();

        $attendanceRate = $monthAttendees > 0
            ? (int) round(($monthPresent / $monthAttendees) * 100)
            : 0;

        $chartMonths = collect();
        for ($i = 5; $i >= 0; $i--) {
            $d = $today->copy()->subMonths($i);
            $chartMonths[$d->format('Y-m')] = Meeting::query()
                ->whereYear('date', $d->year)
                ->whereMonth('date', $d->month)
                ->count();
        }
        $lastMonthsMeetings = $chartMonths->all();

        return [
            Stat::make('غونډې دې میاشت', $meetingsThisMonth)
                ->description('په دې میاشت کې')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays, \Filament\Support\Enums\IconPosition::Before)
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('primary')
                ->chart(array_values($lastMonthsMeetings))
                ->chartColor('primary')
                ->url(MeetingResource::getUrl('index')),

            Stat::make('راتلونکې غونډې', $upcomingCount)
                ->description('تر راتلونکې ۳۰ ورځو')
                ->descriptionIcon(Heroicon::OutlinedCalendar, \Filament\Support\Enums\IconPosition::Before)
                ->icon(Heroicon::OutlinedCalendar)
                ->color('info')
                ->url(MeetingResource::getUrl('index')),

            Stat::make('پرانیستې کارونه', $openTasksCount)
                ->description('نا بشپړې دندې')
                ->descriptionIcon(Heroicon::OutlinedClipboardDocumentList, \Filament\Support\Enums\IconPosition::Before)
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->color('warning')
                ->url(MeetingTaskResource::getUrl('index')),

            Stat::make('ځنډېدلي کارونه', $overdueCount)
                ->description('ځنډېدلي کارونه')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle, \Filament\Support\Enums\IconPosition::Before)
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->color('danger')
                ->url(MeetingTaskResource::getUrl('index')),

            Stat::make('حاضري (دې میاشت)', $monthPresent.' / '.$monthAttendees)
                ->description('حاضر / ټول')
                ->descriptionIcon(Heroicon::OutlinedUserGroup, \Filament\Support\Enums\IconPosition::Before)
                ->icon(Heroicon::OutlinedUserGroup)
                ->color('success'),

            Stat::make('د حاضري سلنه', $attendanceRate.'%')
                ->description('په دې میاشت کې')
                ->descriptionIcon(Heroicon::OutlinedChartBar, \Filament\Support\Enums\IconPosition::Before)
                ->icon(Heroicon::OutlinedChartBar)
                ->color($attendanceRate >= 80 ? 'success' : ($attendanceRate >= 50 ? 'warning' : 'danger')),
        ];
    }
}
