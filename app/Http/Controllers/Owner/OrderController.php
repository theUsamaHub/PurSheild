<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $ownerId = Auth::id();
        $orders = Order::with(['items.product'])
            ->where('owner_id', $ownerId)
            ->latest('order_date')
            ->paginate(15);

        $base = Order::where('owner_id', $ownerId);
        $stats = [
            'total'      => (clone $base)->count(),
            'placed'     => (clone $base)->where('status', 'placed')->count(),
            'processing' => (clone $base)->where('status', 'processing')->count(),
            'completed'  => (clone $base)->where('status', 'completed')->count(),
            'cancelled'  => (clone $base)->where('status', 'cancelled')->count(),
            'spent'      => (clone $base)->where('status', 'completed')->sum('total_amount'),
        ];

        return view('owner.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order): View
    {
        if ($order->owner_id !== Auth::id()) {
            abort(403, 'You are not authorized to view this order.');
        }

        $order->load(['items.product' => function ($q) {
            $q->with('images');
        }]);

        return view('owner.orders.show', compact('order'));
    }
}
