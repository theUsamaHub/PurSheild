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
        $orders = Order::with(['items.product'])
            ->where('owner_id', Auth::id())
            ->latest('order_date')
            ->paginate(15);

        return view('owner.orders.index', compact('orders'));
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
