<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['owner', 'items.product']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        $orders = $query->latest()->paginate(15);

        $stats = [
            'total' => Order::count(),
            'placed' => Order::where('status', 'placed')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'revenue' => Order::whereIn('status', ['completed', 'processing'])->sum('total_amount'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order): View
    {
        $order->load(['owner', 'items.product.images']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:placed,processing,completed,cancelled',
        ]);

        $newStatus = $validated['status'];
        $history = $order->status_history ?? [];
        $history[] = [
            'from' => $order->status,
            'to' => $newStatus,
            'at' => now()->toDateTimeString(),
        ];

        $order->update([
            'status' => $newStatus,
            'status_history' => $history,
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', "Order status updated to {$newStatus}.");
    }
}
