@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="h4 mb-0 fw-semibold">{{ __('Admin Dashboard') }}</h2>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        @foreach ($stats as $stat)
            <div class="col-md-6 col-lg-4 col-xl-3">
                @if ($stat['route'])
                    <a href="{{ route($stat['route']) }}" class="text-decoration-none">
                        <div class="card border-start border-{{ $stat['color'] }} border-4 h-100">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted" style="font-size:0.7rem;">{{ $stat['label'] }}</div>
                                        <div class="fs-4 fw-bold" style="color:var(--bs-body-color);">
                                            @if (!empty($stat['prefix'])){{ $stat['prefix'] }}@endif
                                            {{ $stat['number'] ?? $stat['count'] }}
                                        </div>
                                    </div>
                                    <div class="bg-{{ $stat['color'] }} bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                                        <i class="bi {{ $stat['icon'] }} text-{{ $stat['color'] }}"></i>
                                    </div>
                                </div>
                                @if (!empty($stat['badge']) && $stat['badge'])
                                    <span class="badge bg-{{ $stat['badge_color'] ?? 'danger' }} mt-1" style="font-size:0.65rem;">{{ $stat['badge'] }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @else
                    <div class="card border-start border-{{ $stat['color'] }} border-4 h-100">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted" style="font-size:0.7rem;">{{ $stat['label'] }}</div>
                                    <div class="fs-4 fw-bold">{{ $stat['count'] }}</div>
                                </div>
                                <div class="bg-{{ $stat['color'] }} bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                                    <i class="bi {{ $stat['icon'] }} text-{{ $stat['color'] }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h6 class="mb-0 fw-semibold">{{ __('Users, Contacts & Orders (Last 7 Days)') }}</h6></div>
                <div class="card-body">
                    <canvas id="weeklyChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0 fw-semibold">{{ __('Pets by Species') }}</h6></div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    @if (count($speciesDistribution) > 0)
                        <canvas id="speciesChart" height="180"></canvas>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-heart" style="font-size:2rem;opacity:0.3;"></i>
                            <p class="mt-2 mb-0">{{ __('No pets yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Revenue Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h6 class="mb-0 fw-semibold">{{ __('Revenue (Last 30 Days)') }}</h6></div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Order Status -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0 fw-semibold">{{ __('Order Status') }}</h6></div>
                <div class="card-body">
                    @php
                        $orderTotal = array_sum($orderStatusBreakdown);
                        $statusColors = ['placed' => 'warning', 'processing' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
                        $statusLabels = ['placed' => __('Placed'), 'processing' => __('Processing'), 'completed' => __('Completed'), 'cancelled' => __('Cancelled')];
                    @endphp
                    @if ($orderTotal > 0)
                        @foreach ($statusColors as $status => $color)
                            @php $count = $orderStatusBreakdown[$status] ?? 0; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">{{ $statusLabels[$status] ?? ucfirst($status) }}</small>
                                    <small class="fw-bold">{{ $count }}</small>
                                </div>
                                <div class="progress" style="height:8px;">
                                    <div class="progress-bar bg-{{ $color }}" style="width:{{ ($count / max(1, $orderTotal)) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-bag" style="font-size:2rem;opacity:0.3;"></i>
                            <p class="mt-2 mb-0">{{ __('No orders yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Today -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0 fw-semibold">{{ __('Activity Today') }}</h6></div>
                <div class="card-body">
                    @php
                        $totalToday = $activityToday['total'];
                        $createdToday = $activityToday['created'];
                        $updatedToday = $activityToday['updated'];
                        $deletedToday = $activityToday['deleted'];
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1"><small>{{ __('Created') }}</small><small class="fw-bold text-success">{{ $createdToday }}</small></div>
                        <div class="progress" style="height:8px;"><div class="progress-bar bg-success" style="width:{{ $createdToday > 0 ? 100 : 0 }}%"></div></div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1"><small>{{ __('Updated') }}</small><small class="fw-bold text-warning">{{ $updatedToday }}</small></div>
                        <div class="progress" style="height:8px;"><div class="progress-bar bg-warning" style="width:{{ $updatedToday > 0 ? ($updatedToday / max(1, max($createdToday, max($updatedToday, $deletedToday))) * 100) : 0 }}%"></div></div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1"><small>{{ __('Deleted') }}</small><small class="fw-bold text-danger">{{ $deletedToday }}</small></div>
                        <div class="progress" style="height:8px;"><div class="progress-bar bg-danger" style="width:{{ $deletedToday > 0 ? ($deletedToday / max(1, max($createdToday, max($updatedToday, $deletedToday))) * 100) : 0 }}%"></div></div>
                    </div>
                    <hr>
                    <div class="text-center text-muted" style="font-size:0.8rem;">{{ __('Total: ') . $totalToday . __(' actions today') }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Recent Users') }}</h6>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentUsers as $user)
                        <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold" style="font-size:0.85rem;">{{ $user->name }}</div>
                                    <small class="text-muted" style="font-size:0.7rem;">{{ $user->email }}</small>
                                </div>
                                <div class="text-end">
                                    @foreach ($user->roles->take(2) as $role)
                                        <span class="badge bg-{{ $role->slug === 'admin' ? 'primary' : ($role->slug === 'vet' ? 'success' : ($role->slug === 'shelter' ? 'info' : 'secondary')) }}" style="font-size:0.6rem;">{{ $role->name }}</span>
                                    @endforeach
                                    <div><small class="text-muted" style="font-size:0.65rem;">{{ $user->created_at->diffForHumans() }}</small></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted" style="font-size:0.85rem;">{{ __('No users yet.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Recent Orders') }}</h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentOrders as $order)
                        <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold" style="font-size:0.85rem;">#{{ $order->order_number }}</div>
                                    <small class="text-muted" style="font-size:0.7rem;">{{ $order->owner->name ?? '-' }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'info' : ($order->status === 'cancelled' ? 'danger' : 'warning')) }}" style="font-size:0.6rem;">{{ ucfirst($order->status) }}</span>
                                    <div><small class="fw-bold" style="font-size:0.75rem;">${{ number_format($order->total_amount, 2) }}</small></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted" style="font-size:0.85rem;">{{ __('No orders yet.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header"><h6 class="mb-0 fw-semibold">{{ __('Quick Actions') }}</h6></div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>{{ __('New Product') }}</a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-warning btn-sm"><i class="bi bi-bag me-1"></i>{{ __('Manage Orders') }}</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info btn-sm"><i class="bi bi-people me-1"></i>{{ __('Manage Users') }}</a>
                <a href="{{ route('admin.verification.index') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-person-check me-1"></i>{{ __('Verify Accounts') }}</a>
                <a href="{{ route('admin.backup.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-database me-1"></i>{{ __('Backup') }}</a>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-dark btn-sm"><i class="bi bi-clock-history me-1"></i>{{ __('Activity Log') }}</a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        const weeklyData = @json($chartData['weekly']);
        const revenueData = @json($chartData['monthly_revenue']);
        const speciesData = @json($speciesDistribution);

        // Weekly Users, Contacts & Orders Chart
        new Chart(document.getElementById('weeklyChart'), {
            type: 'bar',
            data: {
                labels: weeklyData.map(d => d.label),
                datasets: [
                    { label: 'Users', data: weeklyData.map(d => d.users), backgroundColor: 'rgba(13,110,253,0.7)', borderRadius: 4 },
                    { label: 'Contacts', data: weeklyData.map(d => d.contacts), backgroundColor: 'rgba(13,202,240,0.7)', borderRadius: 4 },
                    { label: 'Orders', data: weeklyData.map(d => d.orders), backgroundColor: 'rgba(255,193,7,0.7)', borderRadius: 4 },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // Revenue Line Chart
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: revenueData.map(d => d.label),
                datasets: [{
                    label: 'Revenue ($)',
                    data: revenueData.map(d => d.revenue),
                    borderColor: '#1a6b3c',
                    backgroundColor: 'rgba(26,107,60,0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v } } }
            }
        });

        // Species Doughnut Chart
        if (Object.keys(speciesData).length > 0) {
            const speciesColors = ['#1a6b3c','#f59e0b','#0ea5e9','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316'];
            new Chart(document.getElementById('speciesChart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(speciesData),
                    datasets: [{
                        data: Object.values(speciesData),
                        backgroundColor: speciesColors.slice(0, Object.keys(speciesData).length),
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10, font: { size: 11 } } } }
                }
            });
        }
    </script>
    @endpush
@endsection
