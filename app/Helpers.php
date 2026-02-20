<?php

declare(strict_types=1);

/*
 * Here you can define your own helper functions.
 * Make sure to use the `function_exists` check to not declare the function twice.
 */

use Carbon\Carbon;

if (! function_exists('shamsi_date')) {
    /**
     * Format a date as Hijri Shamsi (Jalali).
     *
     * @param  string  $format  Jalali format (e.g. 'Y/m/d')
     */
    function shamsi_date(Carbon|DateTimeInterface|string|null $date, string $format = 'Y/m/d'): string
    {
        if ($date === null || $date === '') {
            return '—';
        }

        $carbon = $date instanceof Carbon
            ? $date
            : ($date instanceof DateTimeInterface
                ? Carbon::instance($date)
                : Carbon::parse($date));

        return Morilog\Jalali\Jalalian::fromCarbon($carbon)->format($format);
    }
}

if (! function_exists('shamsi_datetime')) {
    /**
     * Format a datetime as Hijri Shamsi (Jalali).
     */
    function shamsi_datetime(Carbon|DateTimeInterface|string|null $date): string
    {
        return shamsi_date($date, 'Y/m/d H:i');
    }
}

if (! function_exists('afghan_month_name')) {
    /**
     * Afghan solar calendar month names (حمل، ثور، جوزا، ...).
     *
     * @return array<int, string>
     */
    function afghan_month_names(): array
    {
        return [
            1 => 'حمل',
            2 => 'ثور',
            3 => 'جوزا',
            4 => 'سرطان',
            5 => 'اسد',
            6 => 'سنبله',
            7 => 'میزان',
            8 => 'عقرب',
            9 => 'قوس',
            10 => 'جدی',
            11 => 'دلو',
            12 => 'حوت',
        ];
    }
}

if (! function_exists('shamsi_date_afghan')) {
    /**
     * Format a date in Shamsi with Afghan month name (e.g. یکشنبه، ۱ حمل ۱۴۰۴).
     */
    function shamsi_date_afghan(Carbon|DateTimeInterface|string|null $date): string
    {
        if ($date === null || $date === '') {
            return '—';
        }

        $carbon = $date instanceof Carbon
            ? $date
            : ($date instanceof DateTimeInterface
                ? Carbon::instance($date)
                : Carbon::parse($date));

        $j = Morilog\Jalali\Jalalian::fromCarbon($carbon);
        $months = afghan_month_names();
        $monthNum = (int) $j->format('n');

        return $j->format('l، d').' '.($months[$monthNum] ?? $j->format('F')).' '.$j->format('Y');
    }
}
