<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\View\View;

final class Dashboard extends BaseDashboard
{
    public function getHeader(): ?View
    {
        $user = Filament::auth()->user();
        $now = Carbon::parse(now()->toDateTimeString());
        $hour = (int) $now->format('G');
        $greeting = $hour < 12 ? 'ورځ مو پخیر' : ($hour < 17 ? 'غرمه مو پخیر' : 'ماښام مو پخیر');

        $j = \Morilog\Jalali\Jalalian::fromCarbon($now);
        $months = afghan_month_names();
        $monthNum = (int) $j->format('n');

        return view('filament.pages.dashboard-welcome', [
            'user' => $user,
            'userName' => $user ? Filament::getUserName($user) : '',
            'greeting' => $greeting,
            'dateDayName' => $j->format('l'),
            'dateDay' => $j->format('d'),
            'dateMonthName' => $months[$monthNum] ?? $j->format('F'),
            'dateYear' => $j->format('Y'),
            'dateMonthNum' => $monthNum,
        ]);
    }
}
