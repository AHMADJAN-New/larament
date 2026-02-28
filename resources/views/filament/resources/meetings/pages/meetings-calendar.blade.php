<x-filament-panels::page>
    <style>
        .fi-meetings-calendar-v2 {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .fi-calv2-card {
            border: 1px solid rgb(226 232 240);
            border-radius: 0.9rem;
            background: rgb(255 255 255);
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.05);
        }

        .dark .fi-calv2-card {
            border-color: rgb(51 65 85);
            background: rgb(15 23 42 / 0.7);
            box-shadow: 0 1px 3px rgb(0 0 0 / 0.25);
        }

        .fi-calv2-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.9rem;
            padding: 1rem;
        }

        .fi-calv2-title-wrap {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .fi-calv2-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: rgb(15 23 42);
        }

        .dark .fi-calv2-title {
            color: rgb(248 250 252);
        }

        .fi-calv2-subtitle {
            font-size: 0.8rem;
            color: rgb(100 116 139);
        }

        .dark .fi-calv2-subtitle {
            color: rgb(148 163 184);
        }

        .fi-calv2-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
        }

        .fi-calv2-btn,
        .fi-calv2-select {
            border-radius: 0.65rem;
            border: 1px solid rgb(203 213 225);
            background: rgb(255 255 255);
            color: rgb(51 65 85);
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.5rem 0.7rem;
            transition: 0.15s ease;
        }

        .fi-calv2-btn:hover,
        .fi-calv2-select:hover {
            background: rgb(248 250 252);
        }

        .dark .fi-calv2-btn,
        .dark .fi-calv2-select {
            border-color: rgb(71 85 105);
            background: rgb(15 23 42);
            color: rgb(226 232 240);
        }

        .dark .fi-calv2-btn:hover,
        .dark .fi-calv2-select:hover {
            background: rgb(30 41 59);
        }

        .fi-calv2-btn-primary {
            border-color: transparent;
            background: var(--primary-600, #4f46e5);
            color: #fff;
        }

        .fi-calv2-btn-primary:hover {
            background: var(--primary-500, #6366f1);
        }

        .fi-calv2-stats {
            display: grid;
            gap: 0.75rem;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .fi-calv2-stat {
            padding: 0.85rem 0.9rem;
            border-radius: 0.8rem;
            border: 1px solid rgb(226 232 240);
            background: linear-gradient(145deg, rgb(255 255 255), rgb(248 250 252));
        }

        .dark .fi-calv2-stat {
            border-color: rgb(51 65 85);
            background: linear-gradient(145deg, rgb(15 23 42), rgb(30 41 59));
        }

        .fi-calv2-stat-label {
            font-size: 0.75rem;
            color: rgb(100 116 139);
        }

        .dark .fi-calv2-stat-label {
            color: rgb(148 163 184);
        }

        .fi-calv2-stat-value {
            font-size: 1.3rem;
            font-weight: 700;
            color: rgb(15 23 42);
            line-height: 1.2;
            margin-top: 0.15rem;
        }

        .dark .fi-calv2-stat-value {
            color: rgb(248 250 252);
        }

        .fi-calv2-next-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.35rem;
            font-size: 0.75rem;
            color: var(--primary-700, #4338ca);
            text-decoration: none;
        }

        .fi-calv2-grid-wrap {
            overflow-x: auto;
        }

        .fi-calv2-grid {
            min-width: 760px;
            border-top: 1px solid rgb(226 232 240);
        }

        .dark .fi-calv2-grid {
            border-color: rgb(51 65 85);
        }

        .fi-calv2-weekdays,
        .fi-calv2-week {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
        }

        .fi-calv2-weekdays {
            background: rgb(248 250 252);
            border-bottom: 1px solid rgb(226 232 240);
        }

        .dark .fi-calv2-weekdays {
            background: rgb(15 23 42);
            border-color: rgb(51 65 85);
        }

        .fi-calv2-weekday {
            text-align: center;
            font-size: 0.74rem;
            font-weight: 700;
            color: rgb(100 116 139);
            padding: 0.55rem 0.35rem;
            border-left: 1px solid rgb(226 232 240);
        }

        .dark .fi-calv2-weekday {
            color: rgb(148 163 184);
            border-color: rgb(51 65 85);
        }

        .fi-calv2-weekday:first-child {
            border-left: none;
        }

        .fi-calv2-week {
            border-bottom: 1px solid rgb(226 232 240);
        }

        .dark .fi-calv2-week {
            border-color: rgb(51 65 85);
        }

        .fi-calv2-day {
            min-height: 145px;
            padding: 0.45rem;
            border-left: 1px solid rgb(226 232 240);
            background: rgb(255 255 255);
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .dark .fi-calv2-day {
            border-color: rgb(51 65 85);
            background: rgb(15 23 42 / 0.45);
        }

        .fi-calv2-day:first-child {
            border-left: none;
        }

        .fi-calv2-day.is-other-month {
            background: rgb(248 250 252);
            opacity: 0.78;
        }

        .dark .fi-calv2-day.is-other-month {
            background: rgb(15 23 42);
            opacity: 0.82;
        }

        .fi-calv2-day-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.35rem;
        }

        .fi-calv2-day-num {
            width: 1.9rem;
            height: 1.9rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            color: rgb(51 65 85);
            background: rgb(241 245 249);
        }

        .fi-calv2-day-num.is-today {
            color: #fff;
            background: var(--primary-600, #4f46e5);
        }

        .dark .fi-calv2-day-num {
            color: rgb(226 232 240);
            background: rgb(30 41 59);
        }

        .fi-calv2-day-count {
            font-size: 0.68rem;
            font-weight: 700;
            color: var(--primary-700, #4338ca);
            background: rgb(224 231 255);
            border-radius: 9999px;
            padding: 0.12rem 0.45rem;
            white-space: nowrap;
        }

        .dark .fi-calv2-day-count {
            color: rgb(199 210 254);
            background: rgb(67 56 202 / 0.35);
        }

        .fi-calv2-shamsi {
            font-size: 0.66rem;
            color: rgb(148 163 184);
        }

        .fi-calv2-events {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .fi-calv2-event {
            border-right: 3px solid var(--primary-600, #4f46e5);
            border-radius: 0.5rem;
            background: rgb(238 242 255);
            padding: 0.35rem 0.45rem;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            gap: 0.12rem;
            transition: 0.15s ease;
        }

        .fi-calv2-event:hover {
            background: rgb(224 231 255);
        }

        .dark .fi-calv2-event {
            background: rgb(79 70 229 / 0.22);
            border-right-color: rgb(165 180 252);
        }

        .dark .fi-calv2-event:hover {
            background: rgb(79 70 229 / 0.32);
        }

        .fi-calv2-event-title {
            font-size: 0.72rem;
            color: rgb(15 23 42);
            font-weight: 700;
            line-height: 1.3;
        }

        .dark .fi-calv2-event-title {
            color: rgb(241 245 249);
        }

        .fi-calv2-event-meta {
            font-size: 0.66rem;
            color: rgb(67 56 202);
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex-wrap: wrap;
        }

        .dark .fi-calv2-event-meta {
            color: rgb(199 210 254);
        }

        .fi-calv2-more {
            font-size: 0.66rem;
            color: rgb(100 116 139);
            margin-top: 0.15rem;
        }

        .dark .fi-calv2-more {
            color: rgb(148 163 184);
        }

        @media (max-width: 768px) {
            .fi-calv2-toolbar {
                padding: 0.85rem;
            }

            .fi-calv2-day {
                min-height: 132px;
            }
        }
    </style>

    <div class="fi-meetings-calendar-v2">
        <div class="fi-calv2-card fi-calv2-toolbar">
            <div class="fi-calv2-title-wrap">
                <h2 class="fi-calv2-title">{{ $this->getCalendarHeading() }}</h2>
                <p class="fi-calv2-subtitle">د مجلسونو میاشتنی لید، لنډ احصائیه او چټک لاسرسی</p>
            </div>

            <div class="fi-calv2-controls">
                <button type="button" wire:click="goToPreviousMonth" class="fi-calv2-btn" aria-label="تېر میاشت">‹</button>
                <button type="button" wire:click="goToNextMonth" class="fi-calv2-btn" aria-label="بل میاشت">›</button>

                <select wire:model.live="month" class="fi-calv2-select" aria-label="میاشت">
                    @foreach($this->getMonthOptions() as $monthValue => $monthLabel)
                        <option value="{{ $monthValue }}">{{ $monthLabel }}</option>
                    @endforeach
                </select>

                <select wire:model.live="year" class="fi-calv2-select" aria-label="کال">
                    @foreach($this->getYearOptions() as $yearValue)
                        <option value="{{ $yearValue }}">{{ $yearValue }}</option>
                    @endforeach
                </select>

                <button type="button" wire:click="goToCurrentMonth" class="fi-calv2-btn fi-calv2-btn-primary">نن</button>
                <a href="{{ $this->getCreateUrl() }}" class="fi-calv2-btn fi-calv2-btn-primary">نوی مجلس</a>
                <a href="{{ $this->getListUrl() }}" class="fi-calv2-btn">د مجلسونو لېست</a>
            </div>
        </div>

        <div class="fi-calv2-stats">
            <div class="fi-calv2-stat">
                <div class="fi-calv2-stat-label">ټول مجلسونه (میاشت)</div>
                <div class="fi-calv2-stat-value">{{ $this->totalMeetings }}</div>
            </div>

            <div class="fi-calv2-stat">
                <div class="fi-calv2-stat-label">فعاله ورځې</div>
                <div class="fi-calv2-stat-value">{{ $this->daysWithMeetings }}</div>
            </div>

            <div class="fi-calv2-stat">
                <div class="fi-calv2-stat-label">تر ټولو بوخته ورځ</div>
                <div class="fi-calv2-stat-value">{{ $this->highestDailyMeetings }}</div>
            </div>

            <div class="fi-calv2-stat">
                <div class="fi-calv2-stat-label">راتلونکی مجلس</div>
                @if($this->nextMeeting)
                    <div class="fi-calv2-stat-value">{{ $this->nextMeeting['time_label'] }}</div>
                    <a href="{{ $this->nextMeeting['edit_url'] }}" class="fi-calv2-next-link">
                        {{ \Illuminate\Support\Str::limit($this->nextMeeting['title'], 26) }} | {{ $this->nextMeeting['date_label'] }}
                    </a>
                @else
                    <div class="fi-calv2-stat-value">—</div>
                @endif
            </div>
        </div>

        <div class="fi-calv2-card fi-calv2-grid-wrap">
            <div class="fi-calv2-grid">
                <div class="fi-calv2-weekdays">
                    @foreach($this->getDayLabels() as $dayLabel)
                        <div class="fi-calv2-weekday">{{ $dayLabel }}</div>
                    @endforeach
                </div>

                @foreach($this->weeks as $week)
                    <div class="fi-calv2-week">
                        @foreach($week as $day)
                            <div class="fi-calv2-day {{ $day['is_current_month'] ? 'is-current-month' : 'is-other-month' }}">
                                <div class="fi-calv2-day-head">
                                    <span class="fi-calv2-day-num {{ $day['is_today'] ? 'is-today' : '' }}">{{ $day['day'] }}</span>

                                    @if(count($day['meetings']) > 0)
                                        <span class="fi-calv2-day-count">{{ count($day['meetings']) }} مجلس</span>
                                    @endif
                                </div>

                                <div class="fi-calv2-shamsi">{{ $day['shamsi_date'] }}</div>

                                <div class="fi-calv2-events">
                                    @foreach(array_slice($day['meetings'], 0, 3) as $meeting)
                                        <a href="{{ $meeting['edit_url'] }}" class="fi-calv2-event">
                                            <div class="fi-calv2-event-title">#{{ $meeting['meeting_no'] }} - {{ \Illuminate\Support\Str::limit($meeting['title'], 24) }}</div>
                                            <div class="fi-calv2-event-meta">
                                                <span>{{ $meeting['time_label'] }}</span>
                                                @if(filled($meeting['location']))
                                                    <span>{{ \Illuminate\Support\Str::limit($meeting['location'], 16) }}</span>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>

                                @if(count($day['meetings']) > 3)
                                    <div class="fi-calv2-more">+ {{ count($day['meetings']) - 3 }} نور مجلسونه</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>

