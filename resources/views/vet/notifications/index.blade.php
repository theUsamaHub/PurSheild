@extends('layouts.vet.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Notifications</h3>
            <p class="text-muted mb-0">
                Your recent notifications
            </p>
        </div>
    </div>

    <div class="pc-card">

        @forelse($notifications as $notification)

            @php
                $data = $notification->data ?? [];
            @endphp

            <div class="d-flex align-items-start p-3 border-bottom">

                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:42px;height:42px;background:var(--pc-info-bg);color:var(--pc-info);">

                    <i class="bi bi-bell"></i>

                </div>

                <div class="ms-3 flex-grow-1">

                    <div class="fw-semibold">
                        {{ $data['title'] ?? 'Notification' }}
                    </div>

                    <div class="text-muted small">
                        {{ $data['message'] ?? '' }}
                    </div>

                    <div class="text-muted mt-1" style="font-size:.75rem;">
                        {{ $notification->created_at->diffForHumans() }}
                    </div>

                </div>

            </div>

        @empty

            <div class="text-center py-5">
                <i class="bi bi-bell-slash fs-1 text-muted"></i>

                <p class="text-muted mt-2 mb-0">
                    No notifications yet.
                </p>
            </div>

        @endforelse

    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>

</div>

@endsection