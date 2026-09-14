<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $vetId = Auth::id();

        $todayAppointments = Appointment::where('vet_id', $vetId)
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->with(['pet.species', 'pet.images', 'owner'])
            ->get();

        $pendingCount = Appointment::where('vet_id', $vetId)
            ->where('status', 'pending')
            ->count();

        $completedCount = Appointment::where('vet_id', $vetId)
            ->where('status', 'completed')
            ->count();

        $upcomingQuery = Appointment::where('vet_id', $vetId)
            ->whereIn('status', ['approved', 'pending', 'rescheduled'])
            ->where(function ($query) {
                $query->whereDate('appointment_date', '>', today())
                    ->orWhere(fn ($today) => $today->whereDate('appointment_date', today())->whereTime('appointment_time', '>=', now()->format('H:i:s')));
            })
            ->with(['pet.species', 'pet.images', 'owner'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time');
        $upcomingCount = (clone $upcomingQuery)->count();
        $upcomingAppointments = $upcomingQuery->limit(5)->get();

        $totalPatients = Appointment::where('vet_id', $vetId)
            ->where('status', 'completed')
            ->distinct('pet_id')
            ->count('pet_id');

        $avgRating = Review::where('reviewable_type', \App\Models\User::class)
            ->where('reviewable_id', $vetId)
            ->avg('rating');

        $recentReviews = Review::where('reviewable_type', \App\Models\User::class)
            ->where('reviewable_id', $vetId)
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        $recentNotifications = \App\Support\VetNotifications::query(Auth::user())
            ->orderByDesc('created_at')->orderByDesc('id')->limit(5)->get()
            ->map([\App\Support\VetNotifications::class, 'present']);

        return view('vet.dashboard', compact(
            'todayAppointments', 'pendingCount', 'completedCount',
            'upcomingAppointments', 'upcomingCount', 'totalPatients', 'avgRating', 'recentReviews',
            'recentNotifications'
        ));
    }
}
