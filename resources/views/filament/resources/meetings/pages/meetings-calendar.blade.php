<x-filament-panels::page>
    <style>
        .fi-meetings-calendar { display: block; }
        .fi-meetings-calendar .fi-cal-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }
        .dark .fi-meetings-calendar .fi-cal-toolbar { border-color: #374151; background: #111827; box-shadow: 0 1px 2px rgba(0,0,0,.25); }

        .fi-meetings-calendar .fi-cal-toolbar-left { display: flex; align-items: center; gap: .5rem; }
        .fi-meetings-calendar .fi-cal-toolbar-right { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }

        .fi-meetings-calendar .fi-cal-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: .5rem;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #4b5563;
            transition: background .15s ease, border-color .15s ease;
        }
        .fi-meetings-calendar .fi-cal-icon-btn:hover { background: #f9fafb; }
        .dark .fi-meetings-calendar .fi-cal-icon-btn { border-color: #4b5563; background: #111827; color: #d1d5db; }
        .dark .fi-meetings-calendar .fi-cal-icon-btn:hover { background: #1f2937; }

        .fi-meetings-calendar .fi-cal-title { font-size: 1.125rem; font-weight: 700; color: #111827; }
        .dark .fi-meetings-calendar .fi-cal-title { color: #fff; }

        .fi-meetings-calendar .fi-cal-select {
            border-radius: .5rem;
            border: 1px solid #d1d5db;
            background: #fff;
            padding: .5rem .75rem;
            font-size: .875rem;
            color: #374151;
        }
        .dark .fi-meetings-calendar .fi-cal-select { border-color: #4b5563; background: #1f2937; color: #e5e7eb; }

        .fi-meetings-calendar .fi-cal-btn {
            border-radius: .5rem;
            border: 1px solid #e5e7eb;
            padding: .5rem .75rem;
            font-size: .875rem;
            font-weight: 600;
            color: #374151;
            background: #fff;
            text-decoration: none;
            transition: background .15s ease;
        }
        .fi-meetings-calendar .fi-cal-btn:hover { background: #f9fafb; }
        .dark .fi-meetings-calendar .fi-cal-btn { border-color: #4b5563; color: #e5e7eb; background: #111827; }
        .dark .fi-meetings-calendar .fi-cal-btn:hover { background: #1f2937; }

        .fi-meetings-calendar .fi-cal-btn-primary {
            border-color: transparent;
            background: var(--primary-600, #4f46e5);
            color: #fff;
        }
        .fi-meetings-calendar .fi-cal-btn-primary:hover { background: var(--primary-500, #6366f1); }

        .fi-meetings-calendar .fi-cal-meta { font-size: .875rem; color: #6b7280; }
        .dark .fi-meetings-calendar .fi-cal-meta { color: #9ca3af; }

        .fi-meetings-calendar .fi-cal-grid {
            overflow-x: auto;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }
        .dark .fi-meetings-calendar .fi-cal-grid { border-color: #374151; background: #111827; box-shadow: 0 1px 2px rgba(0,0,0,.25); }
        .fi-meetings-calendar .fi-cal-grid-inner { min-width: 640px; }

        .fi-meetings-calendar .fi-cal-weekdays,
        .fi-meetings-calendar .fi-cal-week { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); }

        .fi-meetings-calendar .fi-cal-weekdays { border-bottom: 1px solid #e5e7eb; background: #f9fafb; }
        .dark .fi-meetings-calendar .fi-cal-weekdays { border-color: #374151; background: rgba(31,41,55,.6); }

        .fi-meetings-calendar .fi-cal-weekday {
            text-align: center;
            padding: .5rem .5rem;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .08em;
            color: #6b7280;
            border-right: 1px solid #e5e7eb;
        }
        .fi-meetings-calendar .fi-cal-weekday:last-child { border-right: none; }
        .dark .fi-meetings-calendar .fi-cal-weekday { color: #9ca3af; border-color: #374151; }

        .fi-meetings-calendar .fi-cal-week { border-bottom: 1px solid #e5e7eb; }
        .fi-meetings-calendar .fi-cal-week:last-child { border-bottom: none; }
        .dark .fi-meetings-calendar .fi-cal-week { border-color: #374151; }

        .fi-meetings-calendar .fi-cal-day {
            min-height: 120px;
            padding: .5rem;
            border-right: 1px solid #e5e7eb;
            background: #fff;
        }
        .fi-meetings-calendar .fi-cal-day:last-child { border-right: none; }
        .fi-meetings-calendar .fi-cal-day.is-other-month { background: rgba(249,250,251,.9); }
        .dark .fi-meetings-calendar .fi-cal-day { border-color: #374151; background: #111827; }
        .dark .fi-meetings-calendar .fi-cal-day.is-other-month { background: rgba(31,41,55,.55); }

        .fi-meetings-calendar .fi-cal-day-head { display:flex; align-items:center; justify-content:space-between; gap:.5rem; margin-bottom:.35rem; }
        .fi-meetings-calendar .fi-cal-day-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 9999px;
            font-size: .875rem;
            font-weight: 700;
            color: #374151;
        }
        .dark .fi-meetings-calendar .fi-cal-day-num { color: #e5e7eb; }
        .fi-meetings-calendar .fi-cal-day-num.is-today { background: var(--primary-600, #4f46e5); color: #fff; }

        .fi-meetings-calendar .fi-cal-shamsi { font-size: .6875rem; color: #9ca3af; }
        .dark .fi-meetings-calendar .fi-cal-shamsi { color: #6b7280; }

        .fi-meetings-calendar .fi-cal-events { display: flex; flex-direction: column; gap: .375rem; }
        .fi-meetings-calendar .fi-cal-event {
            display: block;
            border-radius: .5rem;
            border-left: 3px solid var(--primary-600, #4f46e5);
            background: rgba(79,70,229,.08);
            padding: .375rem .5rem;
            text-decoration: none;
            transition: background .15s ease;
        }
        .fi-meetings-calendar .fi-cal-event:hover { background: rgba(79,70,229,.14); }
        .dark .fi-meetings-calendar .fi-cal-event { border-left-color: var(--primary-400, #a5b4fc); background: rgba(79,70,229,.18); }
        .dark .fi-meetings-calendar .fi-cal-event:hover { background: rgba(79,70,229,.25); }

        .fi-meetings-calendar .fi-cal-event-title { font-size: .75rem; font-weight: 700; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .dark .fi-meetings-calendar .fi-cal-event-title { color: #fff; }
        .fi-meetings-calendar .fi-cal-event-time { font-size: .625rem; color: var(--primary-700, #3730a3); }
        .dark .fi-meetings-calendar .fi-cal-event-time { color: var(--primary-300, #c7d2fe); }
    </style>

    <div class="fi-meetings-calendar">
        {{-- Toolbar: month/year + navigation (like reference: arrows, Today, Month/Year) --}}
        <div class="fi-cal-toolbar">
            <div class="fi-cal-toolbar-left">
                <button
                    type="button"
                    wire:click="goToPreviousMonth"
                    class="fi-cal-icon-btn fi-cal-prev"
                    aria-label="تېر میاشت"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <button
                    type="button"
                    wire:click="goToNextMonth"
                    class="fi-cal-icon-btn fi-cal-next"
                    aria-label="بل میاشت"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <span class="fi-cal-title fi-cal-heading">
                    {{ $this->getCalendarHeading() }}
                </span>
            </div>
            <div class="fi-cal-toolbar-right">
                <select
                    wire:model.live="month"
                    class="fi-cal-select fi-cal-month"
                >
                    @foreach($this->getMonthOptions() as $monthValue => $monthLabel)
                        <option value="{{ $monthValue }}">{{ $monthLabel }}</option>
                    @endforeach
                </select>
                <select
                    wire:model.live="year"
                    class="fi-cal-select fi-cal-year"
                >
                    @foreach($this->getYearOptions() as $yearValue)
                        <option value="{{ $yearValue }}">{{ $yearValue }}</option>
                    @endforeach
                </select>
                <button
                    type="button"
                    wire:click="goToCurrentMonth"
                    class="fi-cal-btn fi-cal-btn-primary fi-cal-today"
                >
                    نن
                </button>
                <a
                    href="{{ $this->getListUrl() }}"
                    class="fi-cal-btn fi-cal-list"
                >
                    د مجلسونو لېست
                </a>
            </div>
        </div>

        <p class="fi-cal-meta">
            ټولې ناستې د دې میاشتې: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $this->totalMeetings }}</span>
        </p>

        {{-- Month grid: 7 columns (weekdays) + rows (weeks) --}}
        <div class="fi-cal-grid">
            <div class="fi-cal-grid-inner">
                {{-- Weekday header --}}
                <div class="fi-cal-weekdays">
                    @foreach($this->getDayLabels() as $dayLabel)
                        <div class="fi-cal-weekday">
                            {{ $dayLabel }}
                        </div>
                    @endforeach
                </div>
                {{-- Week rows --}}
                @forelse($this->weeks as $week)
                    <div class="fi-cal-week">
                        @foreach($week as $day)
                            <div
                                class="fi-cal-day {{ $day['is_current_month'] ? 'is-current-month' : 'is-other-month' }}"
                            >
                                <div class="fi-cal-day-head">
                                    <span
                                        class="fi-cal-day-num {{ $day['is_today'] ? 'is-today' : '' }}"
                                    >
                                        {{ $day['day'] }}
                                    </span>
                                    <span class="fi-cal-shamsi">{{ $day['shamsi_date'] }}</span>
                                </div>
                                <div class="fi-cal-events">
                                    @foreach($day['meetings'] as $meeting)
                                        <a
                                            href="{{ $meeting['edit_url'] }}"
                                            class="fi-cal-event"
                                        >
                                            <span class="fi-cal-event-title" title="{{ $meeting['title'] }}">
                                                {{ Str::limit($meeting['title'], 22) }}
                                            </span>
                                            <span class="fi-cal-event-time">{{ $meeting['time_label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="border-t border-gray-200 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        د دې میاشتې لپاره ورځې نه وې. میاشت او کال بیا وټاکئ.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
