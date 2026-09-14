@extends('layouts.vet.app')
@section('main-class', 'vet-schedule')
@include('vet.partials.schedule-styles')
@section('content')
<div class="vs-heading"><div><h1>{{ __('My Appointments') }}</h1><p>{{ __('Manage, track and review all your booked appointments.') }}</p></div></div>
<form method="GET" action="{{ route('vet.appointments.index') }}" class="vs-filters" id="appointmentFilters" role="search">
    <div class="vs-field"><i class="bi bi-search" aria-hidden="true"></i><label class="visually-hidden" for="appointmentSearch">{{ __('Search by pet, owner or reason') }}</label><input id="appointmentSearch" type="search" name="search" placeholder="{{ __('Search by pet, owner or reason...') }}" value="{{ request('search') }}" maxlength="200"><button class="visually-hidden" type="submit">{{ __('Search') }}</button></div>
    <div class="vs-field"><i class="bi bi-calendar4" aria-hidden="true"></i><label class="visually-hidden" for="appointmentDate">{{ __('Select date') }}</label><input id="appointmentDate" class="{{ request('date') ? '' : 'vs-date-empty' }}" type="date" name="date" value="{{ request('date') }}" aria-label="{{ __('Select date') }}">@unless(request('date'))<span class="vs-date-placeholder">{{ __('Select date') }}</span>@endunless</div>
    <div><label class="visually-hidden" for="appointmentStatus">{{ __('Status') }}</label><select class="form-select" id="appointmentStatus" name="status"><option value="">{{ __('All Statuses') }}</option>@foreach(['pending','approved','rescheduled','completed','cancelled','rejected'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ __(ucfirst($status)) }}</option>@endforeach</select></div>
    <div><label class="visually-hidden" for="appointmentPeriod">{{ __('Date range') }}</label><select class="form-select" name="period" id="appointmentPeriod">@foreach(['week'=>'This Week','today'=>'Today','month'=>'This Month','upcoming'=>'Upcoming','all'=>'All Dates'] as $value=>$label)<option value="{{ $value }}" @selected($period === $value)>{{ __($label) }}</option>@endforeach</select></div>
    <div class="d-flex gap-2"><button class="vs-button vs-button-primary" type="submit">{{ __('Apply') }}</button><a class="vs-button" href="{{ route('vet.appointments.index') }}"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>{{ __('Reset') }}</a></div>
</form>
<section class="vs-panel" aria-labelledby="appointmentsTitle">
    <div class="vs-panel-heading">
        <h2 id="appointmentsTitle"><i class="bi bi-calendar4" aria-hidden="true"></i>{{ __('Appointments') }}</h2>
        <nav class="vs-pagination" aria-label="{{ __('Appointment pages') }}">
            <span>{{ __('Showing :count of :total appointments', ['count'=>$appointments->count(), 'total'=>$appointments->total()]) }}</span>
            @if($appointments->previousPageUrl())<a class="vs-icon-button" href="{{ $appointments->previousPageUrl() }}" aria-label="{{ __('Previous page') }}"><i class="bi bi-chevron-left" aria-hidden="true"></i></a>@else<button class="vs-icon-button" disabled aria-label="{{ __('Previous page') }}"><i class="bi bi-chevron-left" aria-hidden="true"></i></button>@endif
            @if($appointments->nextPageUrl())<a class="vs-icon-button" href="{{ $appointments->nextPageUrl() }}" aria-label="{{ __('Next page') }}"><i class="bi bi-chevron-right" aria-hidden="true"></i></a>@else<button class="vs-icon-button" disabled aria-label="{{ __('Next page') }}"><i class="bi bi-chevron-right" aria-hidden="true"></i></button>@endif
        </nav>
    </div>
    <div class="vs-table-wrap">
        <table class="vs-table vs-appointments-table">
            <thead><tr>@foreach(['Date','Time','Pet','Owner','Reason','Status','Actions'] as $heading)<th scope="col">{{ __($heading) }}</th>@endforeach</tr></thead>
            <tbody>
                @forelse($appointments as $apt)
                <tr>
                    <td><strong>{{ $apt->appointment_date->format('D, M j') }}</strong><small>{{ $apt->appointment_date->format('D') }}</small></td>
                    <td>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i A') }}</td>
                    <td><div class="vs-pet">@include('vet.partials.pet-avatar', ['pet'=>$apt->pet])<div><strong>{{ $apt->pet->name }}</strong><small>{{ $apt->pet->breed?->name ?? $apt->pet->species?->name }}</small></div></div></td>
                    <td>{{ $apt->owner->name }}</td>
                    <td><div class="vs-reason" title="{{ $apt->reason }}">{{ $apt->reason ?: '—' }}</div></td>
                    <td><span class="vs-status vs-status-{{ $apt->status }}">{{ __(ucfirst($apt->status)) }}</span></td>
                    <td><div class="vs-actions">
                        <a class="vs-icon-button" href="{{ route('vet.appointments.show', $apt) }}" title="{{ __('View appointment') }}" aria-label="{{ __('View appointment for :pet', ['pet'=>$apt->pet->name]) }}"><i class="bi bi-eye" aria-hidden="true"></i></a>
                        <button class="vs-icon-button" type="button" data-bs-toggle="modal" data-bs-target="#rescheduleModal" data-url="{{ route('vet.appointments.reschedule', $apt) }}" data-id="{{ $apt->id }}" data-date="{{ $apt->appointment_date->format('Y-m-d') }}" data-time="{{ substr($apt->appointment_time,0,5) }}" data-pet="{{ $apt->pet->name }}" title="{{ __('Reschedule appointment') }}" aria-label="{{ __('Reschedule appointment for :pet', ['pet'=>$apt->pet->name]) }}" @disabled(!in_array($apt->status,['pending','approved','rescheduled']) || $apt->treatment_exists)><i class="bi bi-calendar4" aria-hidden="true"></i></button>
                        <button class="vs-icon-button" type="button" data-bs-toggle="modal" data-bs-target="#actions-{{ $apt->id }}" title="{{ __('More actions') }}" aria-label="{{ __('More actions for :pet', ['pet'=>$apt->pet->name]) }}"><i class="bi bi-three-dots" aria-hidden="true"></i></button>
                    </div></td>
                </tr>
                @empty <tr><td colspan="7"><div class="vs-empty"><i class="bi bi-calendar-x" aria-hidden="true"></i><strong>{{ __('No appointments found') }}</strong><p>{{ __('Try another date range or clear your search filters.') }}</p><a class="vs-button" href="{{ route('vet.appointments.index', ['period'=>'all']) }}">{{ __('View All Dates') }}</a></div></td></tr> @endforelse
            </tbody>
        </table>
    </div>
</section>
@foreach($appointments as $apt)
<div class="modal fade" id="actions-{{ $apt->id }}" tabindex="-1" aria-labelledby="actions-title-{{ $apt->id }}" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content">
    <div class="modal-header"><h2 class="modal-title fs-5" id="actions-title-{{ $apt->id }}">{{ $apt->pet->name }}</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button></div>
    <div class="modal-body d-grid gap-2">
        <a class="vs-button" href="{{ route('vet.appointments.show', $apt) }}">{{ __('View Details') }}</a>
        @if($apt->status === 'pending')
        <form action="{{ route('vet.appointments.approve',$apt) }}" method="POST">@csrf @method('PUT')<button class="vs-button vs-button-primary w-100">{{ __('Approve Appointment') }}</button></form>
        <form action="{{ route('vet.appointments.reject',$apt) }}" method="POST" onsubmit="return confirm('Reject this appointment?')">@csrf @method('PUT')<button class="vs-button text-danger w-100">{{ __('Reject Appointment') }}</button></form>
        @elseif(in_array($apt->status,['approved','rescheduled']))
        @unless($apt->treatment_exists)<a class="vs-button" href="{{ route('vet.treatments.create',$apt) }}">{{ __('Record Treatment') }}</a>@endunless
        @if($apt->treatment_exists)<form action="{{ route('vet.appointments.updateStatus',$apt) }}" method="POST">@csrf @method('PUT')<input type="hidden" name="status" value="completed"><button class="vs-button vs-button-primary w-100">{{ __('Mark Completed') }}</button></form>@endif
        <form action="{{ route('vet.appointments.updateStatus',$apt) }}" method="POST" onsubmit="return confirm('Cancel this appointment?')">@csrf @method('PUT')<input type="hidden" name="status" value="cancelled"><button class="vs-button text-danger w-100">{{ __('Cancel Appointment') }}</button></form>
        @endif
    </div>
</div></div></div>
@endforeach
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form id="rescheduleForm" method="POST">
    @csrf @method('PUT')<input type="hidden" name="reschedule_id" id="rescheduleId">
    <div class="modal-header"><h2 class="modal-title fs-5" id="rescheduleTitle">{{ __('Reschedule Appointment') }}</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button></div>
    <div class="modal-body"><p id="reschedulePet"></p><div class="row g-3"><div class="col-sm-6"><label class="form-label" for="rescheduleDate">{{ __('Date') }}</label><input class="form-control" id="rescheduleDate" name="appointment_date" type="date" min="{{ today()->toDateString() }}" required></div><div class="col-sm-6"><label class="form-label" for="rescheduleTime">{{ __('Time') }}</label><input class="form-control" id="rescheduleTime" name="appointment_time" type="time" required></div></div><p class="small mt-3 mb-0">{{ __('Choose a future time within your weekly availability. Already booked times cannot be selected.') }}</p></div>
    <div class="modal-footer"><button class="vs-button" type="button" data-bs-dismiss="modal">{{ __('Cancel') }}</button><button class="vs-button vs-button-primary">{{ __('Save Changes') }}</button></div>
</form></div></div></div>
@endsection
@push('scripts')
<script>
const filters = document.getElementById('appointmentFilters');
filters.addEventListener('submit', () => {
    if (document.getElementById('appointmentDate').value) document.getElementById('appointmentPeriod').value = 'all';
});
filters.querySelectorAll('select,input[type=date]').forEach(input => input.addEventListener('change', () => {
    if (input.name === 'date' && input.value) document.getElementById('appointmentPeriod').value = 'all';
    if (input.name === 'period') document.getElementById('appointmentDate').value = '';
    filters.requestSubmit();
}));
document.getElementById('appointmentSearch').addEventListener('search', () => filters.requestSubmit());
document.getElementById('rescheduleModal').addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    if (!button) return;
    document.getElementById('rescheduleForm').action = button.dataset.url;
    document.getElementById('rescheduleId').value = button.dataset.id;
    document.getElementById('rescheduleDate').value = button.dataset.date;
    document.getElementById('rescheduleTime').value = button.dataset.time;
    document.getElementById('reschedulePet').textContent = button.dataset.pet;
});
@if(old('reschedule_id'))
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('rescheduleForm').action = @json(url('/vet/appointments')) + '/' + @json(old('reschedule_id')) + '/reschedule';
    document.getElementById('rescheduleId').value = @json(old('reschedule_id'));
    document.getElementById('rescheduleDate').value = @json(old('appointment_date'));
    document.getElementById('rescheduleTime').value = @json(old('appointment_time'));
    bootstrap.Modal.getOrCreateInstance(document.getElementById('rescheduleModal')).show();
});
@endif
</script>
@endpush
