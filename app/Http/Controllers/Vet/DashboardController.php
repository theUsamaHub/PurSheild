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
            ->with(['pet', 'owner'])
            ->get();

        $pendingCount = Appointment::where('vet_id', $vetId)
            ->where('status', 'pending')
            ->count();

        $completedCount = Appointment::where('vet_id', $vetId)
            ->where('status', 'completed')
            ->count();

        $upcomingAppointments = Appointment::where('vet_id', $vetId)
            ->whereIn('status', ['approved', 'pending'])
            ->where('appointment_date', '>=', today())
            ->with(['pet', 'owner'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(5)
            ->get();

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

        return view('vet.dashboard', compact(
            'todayAppointments', 'pendingCount', 'completedCount',
            'upcomingAppointments', 'totalPatients', 'avgRating', 'recentReviews'
        ));
    }
}
