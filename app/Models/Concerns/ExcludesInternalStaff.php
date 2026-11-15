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
     * Employees still with the school on a date (default: today). Someone whose
     * offboarding was approved is archived after their last working day and only
     * shows in Offboarding (Archive).
     */
    public function scopeCurrent(Builder $query, $onDate = null): Builder
    {
        $date = $onDate ? \Carbon\Carbon::parse($onDate)->toDateString() : now()->toDateString();
        $table = $query->getModel()->getTable();

        return $query->where(fn (Builder $q) => $q
            ->whereNull($table.'.separated_on')
            ->orWhere($table.'.separated_on', '>=', $date));
    }
}
