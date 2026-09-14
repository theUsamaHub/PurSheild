@extends('layouts.vet.app')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div><h3 class="fw-bold mb-1">{{ __('Notifications') }}</h3><p class="text-muted mb-0">{{ __('Updates about your practice.') }}</p></div>
    <form method="POST" action="{{ route('vet.notifications.readAll') }}">@csrf @method('PATCH')<button class="btn btn-outline-success" type="submit">{{ __('Mark all as read') }}</button></form>
</div>
<form method="GET" action="{{ route('vet.notifications.index') }}" class="d-flex gap-2 mb-3">
    <select name="status" class="form-select w-auto" aria-label="{{ __('Notification status') }}" onchange="this.form.requestSubmit()">
        @foreach(['all' => 'All notifications', 'unread' => 'Unread', 'read' => 'Read'] as $value => $label)
        <option value="{{ $value }}" @selected(request('status', 'all') === $value)>{{ __($label) }}</option>
        @endforeach
    </select>
    <button class="btn btn-success" type="submit">{{ __('Apply') }}</button>
    <a class="btn btn-outline-secondary" href="{{ route('vet.notifications.index') }}">{{ __('Reset') }}</a>
</form>
<div class="pc-card">
    @forelse($notifications as $notification)
    <div class="d-flex align-items-start flex-wrap gap-3 p-3 border-bottom {{ $notification->is_read ? '' : 'bg-success bg-opacity-10' }}">
        <i class="bi bi-bell fs-4 text-success" aria-hidden="true"></i>
        <div class="flex-grow-1">
            <div class="fw-semibold">{{ $notification->title }} @unless($notification->is_read)<span class="badge bg-success">{{ __('Unread') }}</span>@endunless</div>
            <div class="text-muted small">{{ $notification->message }}</div>
            <div class="text-muted small mt-1">{{ $notification->created_at->diffForHumans() }}</div>
            @if($notification->link)<a class="d-inline-block mt-2" href="{{ $notification->link }}">{{ __('View details') }}</a>@endif
        </div>
        @unless($notification->is_read)
        <form method="POST" action="{{ route('vet.notifications.read', ['source' => $notification->source, 'notification' => $notification->id]) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-success" type="submit">{{ __('Mark as read') }}</button></form>
        @endunless
    </div>
    @empty
    <div class="text-center py-5"><i class="bi bi-bell-slash fs-1 text-muted"></i><p class="text-muted mt-2 mb-0">{{ __('No notifications found.') }}</p></div>
    @endforelse
</div>
<div class="mt-3">{{ $notifications->links() }}</div>
@endsection
