<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Media;
use App\Models\Order;
use App\Models\Pet;
use App\Models\Product;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = $this->getStats();
        $chartData = $this->getChartData();
        $activityToday = $this->getActivityToday();
        $recentUsers = User::with('roles')->latest()->take(5)->get();
        $recentOrders = Order::with(['owner', 'items.product'])->latest()->take(5)->get();
        $topProducts = Product::withCount('orders')
            ->withSum('orders', 'total_amount')
            ->orderByDesc('orders_count')
            ->take(5)
            ->get();
        $speciesDistribution = $this->getSpeciesDistribution();
        $orderStatusBreakdown = $this->getOrderStatusBreakdown();

        return view('admin.dashboard', compact(
            'stats', 'chartData', 'activityToday', 'recentUsers',
            'recentOrders', 'topProducts', 'speciesDistribution', 'orderStatusBreakdown'
        ));
    }

    private function getStats(): array
    {
        $contactCounts = Contact::selectRaw("count(*) as total")
            ->selectRaw("count(case when status = 'new' then 1 end) as new_count")
            ->first();

        $pendingVerifications = User::where('status', 'pending_verification')->count();

        $totalRevenue = Order::whereIn('status', ['completed', 'processing'])->sum('total_amount');
        $pendingOrders = Order::where('status', 'placed')->count();
        $totalProducts = Product::count();
        $outOfStock = Product::where('stock_quantity', '<=', 0)->count();

        return [
            ['label' => __('Users'), 'count' => User::count(), 'icon' => 'bi-people', 'color' => 'primary', 'route' => 'admin.users.index'],
            ['label' => __('Pets'), 'count' => Pet::count(), 'icon' => 'bi-heart', 'color' => 'danger', 'route' => null],
            ['label' => __('Appointments'), 'count' => Appointment::count(), 'icon' => 'bi-calendar-check', 'color' => 'info', 'route' => null],
            ['label' => __('Revenue'), 'number' => number_format($totalRevenue, 2), 'icon' => 'bi-currency-dollar', 'color' => 'success', 'route' => 'admin.orders.index', 'prefix' => '$'],
            ['label' => __('Orders'), 'count' => Order::count(), 'icon' => 'bi-bag', 'color' => 'warning', 'route' => 'admin.orders.index', 'badge' => $pendingOrders . ' ' . __('pending')],
            ['label' => __('Products'), 'count' => $totalProducts, 'icon' => 'bi-box-seam', 'color' => 'primary', 'route' => 'admin.products.index', 'badge' => $outOfStock > 0 ? $outOfStock . ' ' . __('out of stock') : null, 'badge_color' => 'danger'],
            ['label' => __('Contacts'), 'count' => $contactCounts->total, 'icon' => 'bi-envelope', 'color' => 'info', 'route' => 'admin.contacts.index', 'badge' => $contactCounts->new_count],
            ['label' => __('Categories'), 'count' => Category::count(), 'icon' => 'bi-tags', 'color' => 'success', 'route' => 'admin.product-categories.index'],
            ['label' => __('Pending Verifications'), 'count' => $pendingVerifications, 'icon' => 'bi-person-check', 'color' => 'danger', 'route' => 'admin.verification.index'],
            ['label' => __('Subscribers'), 'count' => \App\Models\Subscriber::count(), 'icon' => 'bi-envelope-check', 'color' => 'secondary', 'route' => 'admin.subscribers.index'],
        ];
    }

    private function getChartData(): array
    {
        $userCounts = User::selectRaw("DATE(created_at) as date, count(*) as count")
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->pluck('count', 'date');

        $contactCounts = Contact::selectRaw("DATE(created_at) as date, count(*) as count")
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->pluck('count', 'date');

        $orderCounts = Order::selectRaw("DATE(created_at) as date, count(*) as count")
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->pluck('count', 'date');

        $revenueData = Order::selectRaw("DATE(created_at) as date, sum(total_amount) as revenue")
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->whereIn('status', ['completed', 'processing'])
            ->groupBy('date')
            ->pluck('revenue', 'date');

        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $dateKey = $date->toDateString();
            $days[] = [
                'label' => $date->format('M d'),
                'users' => (int) ($userCounts[$dateKey] ?? 0),
                'contacts' => (int) ($contactCounts[$dateKey] ?? 0),
                'orders' => (int) ($orderCounts[$dateKey] ?? 0),
            ];
        }

        $monthlyRevenue = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $dateKey = $date->toDateString();
            $monthlyRevenue[] = [
                'label' => $date->format('M d'),
                'revenue' => (float) ($revenueData[$dateKey] ?? 0),
            ];
        }

        return [
            'weekly' => $days,
            'monthly_revenue' => $monthlyRevenue,
        ];
    }

    private function getActivityToday(): array
    {
        $events = ActivityLog::selectRaw("event, count(*) as count")
            ->whereDate('created_at', today())
            ->groupBy('event')
            ->pluck('count', 'event');

        $total = $events->sum();

        return [
            'total' => $total,
            'created' => (int) ($events['created'] ?? 0),
            'updated' => (int) ($events['updated'] ?? 0),
            'deleted' => (int) ($events['deleted'] ?? 0),
        ];
    }

    private function getSpeciesDistribution(): array
    {
        return Pet::select('species.name', DB::raw('count(*) as count'))
            ->join('species', 'pets.species_id', '=', 'species.id')
            ->groupBy('species.name')
            ->pluck('count', 'name')
            ->toArray();
    }

    private function getOrderStatusBreakdown(): array
    {
        return Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
}
