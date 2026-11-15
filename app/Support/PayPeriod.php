<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * School pay periods.
 *  - "15th" pay period of a month: 21st of the previous month to the 5th.
 *  - "30th" pay period of a month: 6th to the 20th.
 */
final class PayPeriod
{
    /** @return array{0: string, 1: string} [start Y-m-d, end Y-m-d] */
    public static function range(string $period, string $cutoff): array
    {
        $month = Carbon::createFromFormat('Y-m-d', $period.'-01');

        return $cutoff === '15th'
            ? [$month->copy()->subMonthNoOverflow()->day(21)->toDateString(), $month->copy()->day(5)->toDateString()]
            : [$month->copy()->day(6)->toDateString(), $month->copy()->day(20)->toDateString()];
    }

    /** The pay period running on a date. @return array{0: string, 1: string} [period Y-m, cutoff] */
    public static function at(Carbon $date): array
    {
        if ($date->day <= 5) {
            return [$date->format('Y-m'), '15th'];
        }

        if ($date->day <= 20) {
            return [$date->format('Y-m'), '30th'];
        }

        return [$date->copy()->addMonthNoOverflow()->format('Y-m'), '15th'];
    }

    /** Readable name, e.g. "15th pay period, November 2026". */
    public static function label(string $period, string $cutoff): string
    {
        return $cutoff.' pay period, '.Carbon::createFromFormat('Y-m-d', $period.'-01')->format('F Y');
    }

    /**
     * The current pay period and the ones before it, newest first.
     *
     * @return array<int, array{key: string, period: string, cutoff: string, start: string, end: string, label: string}>
     */
    public static function recent(int $count = 6): array
    {
        [$period, $cutoff] = self::at(now());
        $list = [];

        for ($i = 0; $i < $count; $i++) {
            [$start, $end] = self::range($period, $cutoff);
            $list[] = [
                'key' => $period.'|'.$cutoff,
                'period' => $period,
                'cutoff' => $cutoff,
                'start' => $start,
                'end' => $end,
                'label' => self::label($period, $cutoff).' ('.Carbon::parse($start)->format('M j').' to '.Carbon::parse($end)->format('M j').')',
            ];

            // Step back one pay period.
            if ($cutoff === '30th') {
                $cutoff = '15th';
            } else {
                $cutoff = '30th';
                $period = Carbon::createFromFormat('Y-m-d', $period.'-01')->subMonthNoOverflow()->format('Y-m');
            }
        }

        return $list;
    }
}
