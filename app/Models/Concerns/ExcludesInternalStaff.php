<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait ExcludesInternalStaff
{
    public function scopeExcludeInternalStaff(Builder $query): Builder
    {
        return $query->whereDoesntHave('user', fn (Builder $userQuery) => $userQuery
            ->role(['Admin', 'HR', 'Accounting'])
            ->whereRaw('LOWER(email) LIKE ?', ['%@stmary.edu']));
    }

    /**
     * Employees still with the school on a date (default: today). With a date (payroll, absences), someone whose offboarding was approved
     * still counts until their last working day. Without a date (people lists),
     * they are removed as soon as the offboarding is final approved.
     */
    public function scopeCurrent(Builder $query, $onDate = null): Builder
    {
        $table = $query->getModel()->getTable();

        // Lists of people: once offboarding is final approved, the employee is gone
        // from the lists right away (client request), even before the last working day.
        if ($onDate === null) {
            return $query->whereNull($table.'.separated_on');
        }

        $date = \Carbon\Carbon::parse($onDate)->toDateString();

        return $query->where(fn (Builder $q) => $q
            ->whereNull($table.'.separated_on')
            ->orWhere($table.'.separated_on', '>=', $date));
    }
}
