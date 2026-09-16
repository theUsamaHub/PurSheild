<?php

namespace App\Support;

use App\Models\AdoptionListing;
use App\Models\Appointment;
use App\Models\User;
use App\Models\VetAvailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OwnerDiscovery
{
    public static function vets()
    {
        return User::where('status', 'active')->whereHas('roles', fn ($q) => $q->where('slug', 'vet'))->whereHas('vetProfile', fn ($q) => $q->where('is_verified', true));
    }

    public static function listings()
    {
        return AdoptionListing::whereIn('status', ['available', 'pending'])->whereHas('shelter', fn ($q) => $q->where('status', 'active')->whereHas('roles', fn ($r) => $r->where('slug', 'shelter')));
    }

    public static function favorites(string $kind): array
    {
        return DB::table('owner_favorites')->where('user_id', auth()->id())->where('kind', $kind)->pluck('target_id')->all();
    }

    public static function ageMonths(?string $age): ?float
    {
        if (! $age || ! preg_match('/^\s*(\d+(?:\.\d+)?)\s*(years?|yrs?|y|months?|mos?|m|weeks?|wks?)?\s*$/i', $age, $match)) {
            return null;
        }
        $unit = strtolower($match[2] ?? 'years');

        return (float) $match[1] * (str_starts_with($unit, 'm') ? 1 : (str_starts_with($unit, 'w') ? 12 / 52 : 12));
    }

    public static function distance(float $lat, float $lon, float $destLat, float $destLon): float
    {
        $a = sin(deg2rad($destLat - $lat) / 2) ** 2 + cos(deg2rad($lat)) * cos(deg2rad($destLat)) * sin(deg2rad($destLon - $lon) / 2) ** 2;

        return 6371 * 2 * asin(sqrt(min(1, max(0, $a))));
    }

    public static function availableTimes(int $vetId, Carbon $date, $slots = null, $bookings = null): array
    {
        $slots ??= VetAvailability::where('vet_id', $vetId)->get();
        $bookings ??= Appointment::where('vet_id', $vetId)->whereDate('appointment_date', $date)->whereIn('status', ['pending', 'approved', 'rescheduled'])->pluck('appointment_time')->all();
        $busy = array_map(fn ($time) => substr($time, 0, 5), $bookings);
        $day = strtolower($date->format('l'));
        $times = [];
        foreach ($slots->where('day_of_week', $day)->where('is_available', true) as $slot) {
            $start = Carbon::parse($date->toDateString().' '.$slot->start_time);
            $end = Carbon::parse($date->toDateString().' '.$slot->end_time);
            for ($time = $start->copy(); $time->copy()->addMinutes(30)->lte($end); $time->addMinutes(30)) {
                $blocked = $slots->where('day_of_week', $day)->where('is_available', false)->contains(fn ($s) => $time->format('H:i:s') < $s->end_time && $time->copy()->addMinutes(30)->format('H:i:s') > $s->start_time);
                if (! $blocked && $time->gt(now()) && ! collect($busy)->contains(fn ($b) => abs($time->diffInMinutes(Carbon::parse($date->toDateString().' '.$b), false)) < 30)) {
                    $times[] = $time->format('H:i');
                }
            }
        }
        sort($times);

        return array_values(array_unique($times));
    }
}
