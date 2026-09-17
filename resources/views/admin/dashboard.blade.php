@extends('layouts.app')

@section('page-title', __('PetCare Overview'))

@section('content')
<style>
    :root {
        --admin-card-bg: #ffffff;
        --admin-border-color: #e6f0eb;
        --admin-primary-green: #087657;
        --admin-mint: #e8f7f2;
    }

    .admin-welcome-hero {
        background: linear-gradient(135deg, #074f3e 0%, #0d6b53 60%, #168f6b 100%);
        border-radius: 18px;
        padding: 28px 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(7, 79, 62, 0.18);
        margin-bottom: 28px;
    }

    .admin-welcome-hero::after {
        content: '\f1b0';
        font-family: 'bootstrap-icons';
        position: absolute;
        right: 25px;
        bottom: -20px;
        font-size: 140px;
        color: rgba(255, 255, 255, 0.06);
        pointer-events: none;
    }

    .admin-welcome-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        margin-bottom: 12px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .admin-stat-card {
        background: var(--admin-card-bg);
        border: 1px solid var(--admin-border-color);
        border-radius: 16px;
        padding: 20px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
    }

    .admin-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(8, 118, 87, 0.12);
        border-color: #b8e6d8;
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .stat-green { background: #e6f7f1; color: #087657; }
    .stat-amber { background: #fff8e6; color: #d97706; }
    .stat-blue  { background: #e0f2fe; color: #0284c7; }
    .stat-rose  { background: #ffe4e6; color: #e11d48; }
    .stat-purple{ background: #f3e8ff; color: #9333ea; }
    .stat-teal  { background: #ccfbf1; color: #0d9488; }

    .admin-card {
        background: var(--admin-card-bg);
        border: 1px solid var(--admin-border-color);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .admin-card-header {
        padding: 18px 22px;
        background: #ffffff;
        border-bottom: 1px solid var(--admin-border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .admin-card-header h6 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f3d32;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .admin-quick-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1.5px solid transparent;
    }

    .btn-mint-primary {
        background: #087657;
        color: #ffffff;
    }
    .btn-mint-primary:hover {
        background: #065c44;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(8, 118, 87, 0.25);
    }

    .btn-mint-outline {
        background: #f0fbf7;
        color: #087657;
        border-color: #c0ebe0;
    }
    .btn-mint-outline:hover {
        background: #087657;
        color: #ffffff;
        border-color: #087657;
        transform: translateY(-2px);
    }

    .badge-soft-green { background: #e6f7f1; color: #087657; font-weight: 600; }
    .badge-soft-amber { background: #fef3c7; color: #92400e; font-weight: 600; }
    .badge-soft-blue  { background: #e0f2fe; color: #0369a1; font-weight: 600; }
    .badge-soft-rose  { background: #ffe4e6; color: #9f1239; font-weight: 600; }

    .user-avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #087657, #48ddab);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<!-- Hero Welcome Section -->
<div class="admin-welcome-hero">
    <div class="admin-welcome-badge">
        <i class="bi bi-shield-check"></i> {{ __('FurShield Pet Care Administration') }}
    </div>
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-2 fs-3 text-white">{{ __('Welcome back,') }} {{ Auth::user()->name }}! 🐾</h2>
            <p class="mb-0 text-white fs-6" style="opacity: 0.95;">
                {{ __('Here is a complete summary of your pet care ecosystem — users, clinics, shelters, orders & health records.') }}
            </p>
        </div>

        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('admin.verification.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-success shadow-sm">
                <i class="bi bi-patch-check-fill me-1"></i> {{ __('Pending Verifications') }}
            </a>
        </div>
    </div>
</div>

<!-- Stats Metric Grid -->
<div class="row g-3 mb-4">
    @foreach ($stats as $stat)
        @php
            $colorScheme = match($stat['color'] ?? 'primary') {
                'primary', 'success' => 'stat-green',
                'warning' => 'stat-amber',
                'info' => 'stat-blue',
                'danger' => 'stat-rose',
                'secondary' => 'stat-purple',
                default => 'stat-teal'
            };
        @endphp
        <div class="col-md-6 col-lg-4 col-xl-3">
            @if (!empty($stat['route']))
                <a href="{{ route($stat['route']) }}" class="text-decoration-none">
                    <div class="admin-stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-muted fw-medium d-block mb-1" style="font-size:0.78rem;letter-spacing:0.02em;">
                                    {{ $stat['label'] }}
                                </span>
                                <h3 class="fw-bold mb-0 text-dark" style="font-size:1.6rem;">
                                    @if (!empty($stat['prefix'])){{ $stat['prefix'] }}@endif
                                    {{ $stat['number'] ?? $stat['count'] }}
                                </h3>
                            </div>
                            <div class="stat-icon-wrapper {{ $colorScheme }}">
                                <i class="bi {{ $stat['icon'] }}"></i>
                            </div>
                        </div>
                        @if (!empty($stat['badge']))
                            <div class="mt-3">
                                <span class="badge badge-soft-amber px-2 py-1 rounded-pill" style="font-size:0.7rem;">
                                    <i class="bi bi-exclamation-circle me-1"></i> {{ $stat['badge'] }}
                                </span>
                            </div>
                        @endif
                    </div>
                </a>
            @else
                <div class="admin-stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted fw-medium d-block mb-1" style="font-size:0.78rem;">{{ $stat['label'] }}</span>
                            <h3 class="fw-bold mb-0 text-dark" style="font-size:1.6rem;">{{ $stat['count'] }}</h3>
                        </div>
                        <div class="stat-icon-wrapper {{ $colorScheme }}">
                            <i class="bi {{ $stat['icon'] }}"></i>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>

<!-- Main Analytics & Charts -->
<div class="row g-4 mb-4">
    <!-- Weekly Analytics Bar Chart -->
    <div class="col-lg-8">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h6><i class="bi bi-graph-up-arrow text-success"></i> {{ __('Ecosystem Activity (Last 7 Days)') }}</h6>
                <span class="badge bg-light text-muted border px-3 py-1 rounded-pill" style="font-size:0.75rem;">{{ __('Users, Contacts & Orders') }}</span>
            </div>
            <div class="card-body p-4">
                <canvas id="weeklyChart" height="260"></canvas>
            </div>
        </div>
    </div>

    <!-- Species Distribution Chart -->
    <div class="col-lg-4">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h6><i class="bi bi-heart-pulse text-danger"></i> {{ __('Registered Pets by Species') }}</h6>
            </div>
            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                @if (count($speciesDistribution) > 0)
                    <div style="position:relative; width:100%; height:230px;">
                        <canvas id="speciesChart"></canvas>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="stat-icon-wrapper stat-green mx-auto mb-3" style="width:60px;height:60px;font-size:1.8rem;">
                            <i class="bi bi-paw-fill"></i>
                        </div>
                        <p class="text-muted mb-0 fw-medium">{{ __('No pets registered yet in the system.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Revenue & Orders Overview -->
<div class="row g-4 mb-4">
    <!-- Monthly Revenue Line Chart -->
    <div class="col-lg-8">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h6><i class="bi bi-cash-stack text-success"></i> {{ __('Pet Shop Revenue (Last 30 Days)') }}</h6>
            </div>
            <div class="card-body p-4">
                <canvas id="revenueChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <!-- Order Status Breakdown -->
    <div class="col-lg-4">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h6><i class="bi bi-bag-check text-primary"></i> {{ __('Order Fulfillment Status') }}</h6>
            </div>
            <div class="card-body p-4">
                @php
                    $orderTotal = array_sum($orderStatusBreakdown);
                    $statusConfig = [
                        'placed' => ['color' => '#d97706', 'bg' => 'bg-warning', 'label' => __('Placed')],
                        'processing' => ['color' => '#0284c7', 'bg' => 'bg-info', 'label' => __('Processing')],
                        'completed' => ['color' => '#087657', 'bg' => 'bg-success', 'label' => __('Completed')],
                        'cancelled' => ['color' => '#e11d48', 'bg' => 'bg-danger', 'label' => __('Cancelled')]
                    ];
                @endphp
                @if ($orderTotal > 0)
                    @foreach ($statusConfig as $status => $cfg)
                        @php $count = $orderStatusBreakdown[$status] ?? 0; $perc = round(($count / max(1, $orderTotal)) * 100); @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark" style="font-size:0.85rem;">{{ $cfg['label'] }}</span>
                                <span class="badge bg-light text-dark border fw-bold">{{ $count }} ({{ $perc }}%)</span>
                            </div>
                            <div class="progress rounded-pill" style="height:8px; background:#f0f4f2;">
                                <div class="progress-bar {{ $cfg['bg'] }} rounded-pill" style="width:{{ $perc }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-bag-x text-muted" style="font-size:2.5rem; opacity:0.4;"></i>
                        <p class="text-muted mt-2 mb-0">{{ __('No orders recorded yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- System Activity & Recent Tables -->
<div class="row g-4 mb-4">
    <!-- Today's Activity Bar -->
    <div class="col-lg-4">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h6><i class="bi bi-activity text-info"></i> {{ __('System Activity Today') }}</h6>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                @php
                    $totalToday = $activityToday['total'];
                    $createdToday = $activityToday['created'];
                    $updatedToday = $activityToday['updated'];
                    $deletedToday = $activityToday['deleted'];
                    $maxAct = max(1, max($createdToday, max($updatedToday, $deletedToday)));
                @endphp
                <div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted" style="font-size:0.85rem;">{{ __('New Records Created') }}</span>
                            <span class="fw-bold text-success">{{ $createdToday }}</span>
                        </div>
                        <div class="progress rounded-pill" style="height:7px; background:#f0f4f2;">
                            <div class="progress-bar bg-success rounded-pill" style="width:{{ ($createdToday / $maxAct) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted" style="font-size:0.85rem;">{{ __('Records Updated') }}</span>
                            <span class="fw-bold text-warning">{{ $updatedToday }}</span>
                        </div>
                        <div class="progress rounded-pill" style="height:7px; background:#f0f4f2;">
                            <div class="progress-bar bg-warning rounded-pill" style="width:{{ ($updatedToday / $maxAct) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted" style="font-size:0.85rem;">{{ __('Records Deleted') }}</span>
                            <span class="fw-bold text-danger">{{ $deletedToday }}</span>
                        </div>
                        <div class="progress rounded-pill" style="height:7px; background:#f0f4f2;">
                            <div class="progress-bar bg-danger rounded-pill" style="width:{{ ($deletedToday / $maxAct) * 100 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-3 mt-3 text-center" style="background:#f4fbf8; border:1px solid #e0f2ec;">
                    <i class="bi bi-clock-history me-1 text-success"></i>
                    <strong class="text-dark">{{ $totalToday }}</strong> <span class="text-muted" style="font-size:0.82rem;">{{ __('actions logged today') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="col-lg-4">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h6><i class="bi bi-people-fill text-primary"></i> {{ __('Recent Registered Users') }}</h6>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light text-success fw-semibold border px-3 rounded-pill">{{ __('View All') }}</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentUsers as $user)
                    <div class="px-3 py-3 border-bottom d-flex align-items-center gap-3">
                        <div class="user-avatar-circle flex-shrink-0">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-dark text-truncate" style="font-size:0.88rem;">{{ $user->name }}</div>
                            <small class="text-muted text-truncate d-block" style="font-size:0.75rem;">{{ $user->email }}</small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            @foreach ($user->roles->take(1) as $role)
                                <span class="badge badge-soft-green mb-1 d-block" style="font-size:0.65rem;">{{ $role->name }}</span>
                            @endforeach
                            <small class="text-muted d-block" style="font-size:0.68rem;">{{ $user->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">{{ __('No users yet.') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-4">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h6><i class="bi bi-cart-check text-warning"></i> {{ __('Recent Shop Orders') }}</h6>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light text-success fw-semibold border px-3 rounded-pill">{{ __('View All') }}</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentOrders as $order)
                    <div class="px-3 py-3 border-bottom d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper stat-blue flex-shrink-0" style="width:38px;height:38px;font-size:1rem;">
                            <i class="bi bi-bag"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-dark text-truncate" style="font-size:0.88rem;">#{{ $order->order_number }}</div>
                            <small class="text-muted text-truncate d-block" style="font-size:0.75rem;">{{ $order->owner->name ?? 'Guest' }}</small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge badge-soft-green mb-1 d-block" style="font-size:0.68rem;">${{ number_format($order->total_amount, 2) }}</span>
                            <small class="text-muted d-block" style="font-size:0.68rem;">{{ ucfirst($order->status) }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">{{ __('No orders yet.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- PetCare Quick Actions Palette -->
<div class="admin-card">
    <div class="admin-card-header">
        <h6><i class="bi bi-lightning-charge-fill text-warning"></i> {{ __('Quick Management Actions') }}</h6>
    </div>
    <div class="card-body p-4">
        <div class="d-flex flex-wrap gap-3">
            <a href="{{ route('admin.products.create') }}" class="admin-quick-btn btn-mint-primary">
                <i class="bi bi-plus-circle"></i> {{ __('Add Product') }}
            </a>
            <a href="{{ route('admin.verification.index') }}" class="admin-quick-btn btn-mint-outline">
                <i class="bi bi-patch-check"></i> {{ __('Verify Vets & Shelters') }}
            </a>
            <a href="{{ route('admin.orders.index') }}" class="admin-quick-btn btn-mint-outline">
                <i class="bi bi-bag"></i> {{ __('Manage Orders') }}
            </a>
            <a href="{{ route('admin.users.index') }}" class="admin-quick-btn btn-mint-outline">
                <i class="bi bi-people"></i> {{ __('Users Directory') }}
            </a>
            <a href="{{ route('admin.care-content.index') }}" class="admin-quick-btn btn-mint-outline">
                <i class="bi bi-journal-richtext"></i> {{ __('Care Articles') }}
            </a>
            <a href="{{ route('admin.activity-logs.index') }}" class="admin-quick-btn btn-mint-outline">
                <i class="bi bi-clock-history"></i> {{ __('Activity Logs') }}
            </a>
            <a href="{{ route('admin.backup.index') }}" class="admin-quick-btn btn-mint-outline">
                <i class="bi bi-database"></i> {{ __('System Backup') }}
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const weeklyData = @json($chartData['weekly']);
    const revenueData = @json($chartData['monthly_revenue']);
    const speciesData = @json($speciesDistribution);

    // Weekly Activity Bar Chart
    new Chart(document.getElementById('weeklyChart'), {
        type: 'bar',
        data: {
            labels: weeklyData.map(d => d.label),
            datasets: [
                { label: 'New Users', data: weeklyData.map(d => d.users), backgroundColor: '#087657', borderRadius: 6 },
                { label: 'Inquiries', data: weeklyData.map(d => d.contacts), backgroundColor: '#0ea5e9', borderRadius: 6 },
                { label: 'Orders', data: weeklyData.map(d => d.orders), backgroundColor: '#f59e0b', borderRadius: 6 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { family: 'Poppins', size: 12 } } }
            },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: '#f0f4f2' }, ticks: { stepSize: 1 } }
            }
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
                borderColor: '#087657',
                borderWidth: 2.5,
                backgroundColor: (context) => {
                    const ctx = context.chart.ctx;
                    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                    gradient.addColorStop(0, 'rgba(8, 118, 87, 0.25)');
                    gradient.addColorStop(1, 'rgba(8, 118, 87, 0.00)');
                    return gradient;
                },
                fill: true,
                tension: 0.35,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#087657'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: '#f0f4f2' }, ticks: { callback: v => '$' + v } }
            }
        }
    });

    // Species Doughnut Chart
    if (Object.keys(speciesData).length > 0) {
        const speciesColors = ['#087657','#f59e0b','#0ea5e9','#e11d48','#8b5cf6','#ec4899','#14b8a6','#f97316'];
        new Chart(document.getElementById('speciesChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(speciesData),
                datasets: [{
                    data: Object.values(speciesData),
                    backgroundColor: speciesColors.slice(0, Object.keys(speciesData).length),
                    borderWidth: 3,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { family: 'Poppins', size: 11 } } }
                }
            }
        });
    }
</script>
@endpush
@endsection
