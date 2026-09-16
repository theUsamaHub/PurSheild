<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class OwnerPetHealth
{
    // Only the most recent administration of each vaccine determines its next due date.
    public static function due(Builder $query): Builder
    {
        return self::latestVaccines($query)->whereNotNull('next_due_date')->whereDate('next_due_date', '<=', today()->addDays(30));
    }

    public static function latestVaccines(Builder $query): Builder
    {
        return $query->whereNotExists(function ($q) {
            $q->selectRaw('1')->from('vaccinations as newer')->whereColumn('newer.pet_id', 'vaccinations.pet_id')
                ->whereColumn('newer.vaccine_name', 'vaccinations.vaccine_name')->where(function ($q) {
                    $q->whereColumn('newer.vaccination_date', '>', 'vaccinations.vaccination_date')
                        ->orWhere(fn ($q) => $q->whereColumn('newer.vaccination_date', 'vaccinations.vaccination_date')->whereColumn('newer.id', '>', 'vaccinations.id'));
                });
        });
    }
}
