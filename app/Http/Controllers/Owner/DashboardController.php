<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $stats = [
            'pets' => Pet::where('owner_id', $userId)->count(),
            'upcoming_appointments' => Appointment::where('owner_id', $userId)
                ->where('status', '!=', 'cancelled')
                ->where('appointment_date', '>=', now()->toDateString())
                ->count(),
            'pending_appointments' => Appointment::where('owner_id', $userId)
                ->where('status', 'pending')
                ->count(),
            'active_cart_items' => Cart::where('owner_id', $userId)
                ->withCount('items')
                ->first()?->items_count ?? 0,
            'total_orders' => Order::where('owner_id', $userId)->count(),
        ];

        $recentAppointments = Appointment::with(['pet', 'vet'])
            ->where('owner_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->latest('appointment_date')
            ->limit(5)
            ->get();

        $pets = Pet::with(['species', 'breed'])
            ->where('owner_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        $recentOrders = Order::with('items')
            ->where('owner_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        return view('owner.dashboard', compact('stats', 'recentAppointments', 'pets', 'recentOrders'));
    }
}
