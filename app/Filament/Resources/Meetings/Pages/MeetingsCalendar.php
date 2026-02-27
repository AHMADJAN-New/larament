<?php

declare(strict_types=1);

namespace App\Filament\Resources\Meetings\Pages;

use App\Filament\Resources\Meetings\MeetingResource;
use App\Models\Meeting;
use BackedEnum;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;

final class MeetingsCalendar extends Page
{
    #[Url(as: 'month')]
    public int $month;

    #[Url(as: 'year')]
    public int $year;

    /**
     * @var array<int, array<int, array{
     *     day: int,
     *     date_key: string,
     *     shamsi_date: string,
     *     is_current_month: bool,
     *     is_today: bool,
     *     meetings: list<array{id: int, meeting_no: int, title: string, time_label: string, location: string, edit_url: string}>
     * }>>
     */
    public array $weeks = [];

    public int $totalMeetings = 0;

    protected static string $resource = MeetingResource::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $title = 'د مجلسونو کلینډر';

    protected string $view = 'filament.resources.meetings.pages.meetings-calendar';

    public function mount(): void
    {
        $this->ensureMonthYear();
        $this->loadCalendar();
    }

    public function ensureMonthYear(): void
    {
        $today = CarbonImmutable::today();
        if (! isset($this->month) || $this->month < 1 || $this->month > 12) {
            $this->month = (int) $today->format('n');
        }
        if (! isset($this->year) || $this->year < 1970 || $this->year > 2100) {
            $this->year = (int) $today->format('Y');
        }
        $this->normalizePeriod();
    }

    public function updatedMonth(): void
    {
        $this->normalizePeriod();
        $this->loadCalendar();
    }

    public function updatedYear(): void
    {
        $this->normalizePeriod();
        $this->loadCalendar();
    }

    public function goToPreviousMonth(): void
    {
        $period = CarbonImmutable::create($this->year, $this->month, 1)->subMonth();

        $this->month = (int) $period->format('n');
        $this->year = (int) $period->format('Y');
        $this->loadCalendar();
    }

    public function goToNextMonth(): void
    {
        $period = CarbonImmutable::create($this->year, $this->month, 1)->addMonth();

        $this->month = (int) $period->format('n');
        $this->year = (int) $period->format('Y');
        $this->loadCalendar();
    }

    public function goToCurrentMonth(): void
    {
        $today = CarbonImmutable::today();

        $this->month = (int) $today->format('n');
        $this->year = (int) $today->format('Y');
        $this->loadCalendar();
    }

    /**
     * @return array<int, string>
     */
    public function getDayLabels(): array
    {
        return ['شنبه', 'یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
    }

    /**
     * @return array<int, string>
     */
    public function getMonthOptions(): array
    {
        return [
            1 => 'جنوري',
            2 => 'فبروري',
            3 => 'مارچ',
            4 => 'اپرېل',
            5 => 'مې',
            6 => 'جون',
            7 => 'جولای',
            8 => 'اګست',
            9 => 'سپټمبر',
            10 => 'اکتوبر',
            11 => 'نومبر',
            12 => 'دسمبر',
        ];
    }

    /**
     * @return array<int, int>
     */
    public function getYearOptions(): array
    {
        $meetingYears = Meeting::query()
            ->whereNotNull('date')
            ->orderBy('date')
            ->pluck('date')
            ->map(static fn (mixed $date): int => (int) CarbonImmutable::parse((string) $date)->format('Y'))
            ->unique()
            ->values()
            ->all();

        $currentYear = (int) CarbonImmutable::today()->format('Y');

        if ($meetingYears === []) {
            $meetingYears = range($currentYear - 2, $currentYear + 2);
        }

        if (! in_array($currentYear, $meetingYears, true)) {
            $meetingYears[] = $currentYear;
        }

        if (! in_array($this->year, $meetingYears, true)) {
            $meetingYears[] = $this->year;
        }

        sort($meetingYears);

        return $meetingYears;
    }

    public function getCalendarHeading(): string
    {
        $months = $this->getMonthOptions();

        return ($months[$this->month] ?? '').' '.$this->year;
    }

    public function getListUrl(): string
    {
        return MeetingResource::getUrl('index');
    }

    private function normalizePeriod(): void
    {
        $this->month = max(1, min(12, $this->month));
        $this->year = max(1970, min(2100, $this->year));
    }

    private function loadCalendar(): void
    {
        $this->ensureMonthYear();

        $monthStart = CarbonImmutable::create($this->year, $this->month, 1)->startOfDay();
        $monthEnd = $monthStart->endOfMonth();
        $calendarStart = $monthStart->startOfWeek(CarbonImmutable::SATURDAY);
        $calendarEnd = $monthEnd->endOfWeek(CarbonImmutable::FRIDAY);

        $meetings = Meeting::query()
            ->whereDate('date', '>=', $monthStart->toDateString())
            ->whereDate('date', '<=', $monthEnd->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $this->totalMeetings = $meetings->count();

        $meetingsByDate = $meetings
            ->groupBy(static fn (Meeting $meeting): string => $meeting->date->toDateString())
            ->map(
                static fn ($items): array => $items
                    ->map(
                        static fn (Meeting $meeting): array => [
                            'id' => $meeting->id,
                            'meeting_no' => $meeting->meeting_no,
                            'title' => $meeting->title,
                            'time_label' => filled($meeting->time) ? Carbon::parse((string) $meeting->time)->format('H:i') : '—',
                            'location' => (string) ($meeting->location ?? ''),
                            'edit_url' => MeetingResource::getUrl('edit', ['record' => $meeting]),
                        ]
                    )
                    ->values()
                    ->all()
            )
            ->all();

        $weeks = [];
        $cursor = $calendarStart;

        while ($cursor->lessThanOrEqualTo($calendarEnd)) {
            $week = [];

            for ($dayIndex = 0; $dayIndex < 7; $dayIndex++) {
                $dateKey = $cursor->toDateString();

                $week[] = [
                    'day' => (int) $cursor->format('j'),
                    'date_key' => $dateKey,
                    'shamsi_date' => shamsi_date($cursor->toDateString()),
                    'is_current_month' => (int) $cursor->format('n') === $this->month,
                    'is_today' => $cursor->isSameDay(CarbonImmutable::today()),
                    'meetings' => $meetingsByDate[$dateKey] ?? [],
                ];

                $cursor = $cursor->addDay();
            }

            $weeks[] = $week;
        }

        $this->weeks = $weeks;
    }
}
