<div class="fi-dashboard-welcome">
    <div class="fi-dashboard-welcome-card">
        <div class="fi-dashboard-welcome-card-inner">
            <div class="fi-dashboard-welcome-greeting">{{ $greeting }}</div>
            @if($userName)
                <h1 class="fi-dashboard-welcome-name">{{ $userName }}</h1>
            @endif
            <div class="fi-dashboard-welcome-date-ctn">
                <span class="fi-dashboard-welcome-dayname">{{ $dateDayName }}،</span>
                <span class="fi-dashboard-welcome-day">{{ $dateDay }}</span>
                <span class="fi-dashboard-welcome-month fi-dashboard-welcome-month-{{ $dateMonthNum }}">{{ $dateMonthName }}</span>
                <span class="fi-dashboard-welcome-year">{{ $dateYear }}</span>
            </div>
        </div>
        <div class="fi-dashboard-welcome-accent" aria-hidden="true"></div>
    </div>
</div>
