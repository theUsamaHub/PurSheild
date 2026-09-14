@extends('layouts.vet.app')
@section('main-class', 'vet-schedule')
@include('vet.partials.schedule-styles')

@section('content')
<div class="vs-heading">
    <div><h1>{{ __('My Availability') }}</h1><p>{{ __('Manage your available days and time slots for appointments.') }}</p></div>
    <button type="button" class="vs-button vs-button-primary vs-add" onclick="openSlotModal()"><i class="bi bi-plus-lg" aria-hidden="true"></i>{{ __('Add New Slot') }}</button>
</div>
@if($errors->any())
    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
@endif
<div class="vs-stats">
    @foreach([[$activeDays, 'Active Days', 'calendar-event', '', 'Days with an available time slot.'], [$availableSlotsThisWeek, 'Available Slots This Week', 'clock', 'blue', 'Recurring available time ranges in your weekly schedule.'], [$blockedDates, 'Blocked Dates', 'slash-circle', 'orange', 'Weekly days with explicitly unavailable slots; this schedule repeats each week.']] as [$count, $label, $icon, $tone, $hint])
        <div class="vs-stat {{ $tone ? 'vs-stat-'.$tone : '' }}" title="{{ __($hint) }}">
            <div class="vs-stat-icon"><i class="bi bi-{{ $icon }}" aria-hidden="true"></i></div>
            <div><strong>{{ $count }}</strong><div class="vs-stat-label">{{ __($label) }}</div></div>
            <span class="vs-stat-chevron" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
        </div>
    @endforeach
</div>
<section class="vs-panel" aria-labelledby="weekly-title">
    <div class="vs-panel-heading">
        <h2 id="weekly-title"><i class="bi bi-calendar4" aria-hidden="true"></i>{{ __('Weekly Availability') }}</h2>
        <button class="vs-button" type="button" data-bs-toggle="modal" data-bs-target="#copyModal"><i class="bi bi-copy" aria-hidden="true"></i>{{ __('Copy Week Schedule') }}</button>
    </div>
    <div class="vs-table-wrap">
        <table class="vs-table vs-week-table">
            <thead><tr><th scope="col">{{ __('Day') }}</th><th scope="col">{{ __('Status') }}</th><th scope="col">{{ __('Available Time Slots') }}</th><th scope="col">{{ __('Actions') }}</th></tr></thead>
            <tbody>
            @foreach($days as $day)
                @php
                    $daySlots = $availability->get($day, collect());
                    $visibleSlots = $daySlots->where('is_available', true);
                    $available = $visibleSlots->isNotEmpty();
                @endphp
                <tr>
                    <td><strong>{{ __(ucfirst($day)) }}</strong><small>{{ __(ucfirst(substr($day, 0, 3))) }}</small></td>
                    <td><span class="vs-status {{ $available ? '' : 'vs-status-unavailable' }}">{{ __($available ? 'Available' : 'Unavailable') }}</span></td>
                    <td><div class="vs-slots">
                        @forelse($visibleSlots as $slot)
                            <span class="vs-slot">{{ $slot->start_time ? \Carbon\Carbon::parse($slot->start_time)->format('g:i A') : '—' }} &ndash; {{ $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('g:i A') : '—' }}</span>
                        @empty <span>&mdash;</span> @endforelse
                    </div></td>
                    <td class="text-center"><button class="vs-icon-button vs-edit" type="button" data-bs-toggle="modal" data-bs-target="#day-{{ $day }}" title="{{ __('Edit :day availability', ['day' => ucfirst($day)]) }}" aria-label="{{ __('Edit :day availability', ['day' => ucfirst($day)]) }}"><i class="bi bi-pencil" aria-hidden="true"></i></button></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</section>

@foreach($days as $day)
<div class="modal fade" id="day-{{ $day }}" tabindex="-1" aria-labelledby="day-title-{{ $day }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h2 class="modal-title fs-5" id="day-title-{{ $day }}">{{ __(ucfirst($day)) }} {{ __('Availability') }}</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button></div>
        <div class="modal-body">
            @forelse($availability->get($day, collect()) as $slot)
            <div class="vs-editor-slot">
                <div>{{ $slot->start_time ? \Carbon\Carbon::parse($slot->start_time)->format('g:i A') : '—' }} &ndash; {{ $slot->end_time ? \Carbon\Carbon::parse($slot->end_time)->format('g:i A') : '—' }}<br><span class="vs-status {{ $slot->is_available ? '' : 'vs-status-unavailable' }}">{{ __($slot->is_available ? 'Available' : 'Unavailable') }}</span></div>
                <div class="vs-actions">
                    <button class="vs-icon-button" type="button" aria-label="{{ __('Edit slot') }}" onclick="openSlotModal('{{ $day }}', {{ $slot->id }}, '{{ substr($slot->start_time ?? '09:00', 0, 5) }}', '{{ substr($slot->end_time ?? '17:00', 0, 5) }}', {{ $slot->is_available ? 1 : 0 }})"><i class="bi bi-pencil" aria-hidden="true"></i></button>
                    <form method="POST" action="{{ route('vet.availability.destroy', $slot) }}" onsubmit="return confirm('Remove this time slot?')">@csrf @method('DELETE')<button class="vs-icon-button text-danger" aria-label="{{ __('Delete slot') }}"><i class="bi bi-trash" aria-hidden="true"></i></button></form>
                </div>
            </div>
            @empty <p class="mb-0">{{ __('No slots added for this day. Add a time range to become available.') }}</p> @endforelse
        </div>
        <div class="modal-footer"><button class="vs-button" type="button" data-bs-dismiss="modal">{{ __('Close') }}</button><button class="vs-button vs-button-primary" type="button" onclick="openSlotModal('{{ $day }}')"><i class="bi bi-plus-lg" aria-hidden="true"></i>{{ __('Add Slot') }}</button></div>
    </div></div>
</div>
@endforeach

<div class="modal fade" id="slotModal" tabindex="-1" aria-labelledby="slotModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form id="slotForm" method="POST" action="{{ route('vet.availability.store') }}">
            @csrf <input type="hidden" name="_method" id="formMethod" value="POST"><input type="hidden" name="slot_id" id="slotId">
            <div class="modal-header"><h2 class="modal-title fs-5" id="slotModalTitle">{{ __('Add New Slot') }}</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label for="slotDay" class="form-label">{{ __('Day') }}</label><select class="form-select" name="day_of_week" id="slotDay" required>@foreach($days as $day)<option value="{{ $day }}">{{ __(ucfirst($day)) }}</option>@endforeach</select></div>
                <div class="row g-3">
                    <div class="col-6"><label for="slotStart" class="form-label">{{ __('Start Time') }}</label><input class="form-control" type="time" name="start_time" id="slotStart" required></div>
                    <div class="col-6"><label for="slotEnd" class="form-label">{{ __('End Time') }}</label><input class="form-control" type="time" name="end_time" id="slotEnd" required></div>
                </div>
                <p id="timeError" class="text-danger mt-2 mb-0" role="alert" hidden>{{ __('End time must be at least 15 minutes after start time.') }}</p>
                <div class="mt-3"><label for="slotStatus" class="form-label">{{ __('Status') }}</label><select class="form-select" name="is_available" id="slotStatus"><option value="1">{{ __('Available') }}</option><option value="0">{{ __('Unavailable') }}</option></select></div>
                <p class="small mt-3 mb-0">{{ __('This time range repeats every week. Existing appointments are kept when availability changes.') }}</p>
            </div>
            <div class="modal-footer"><button type="button" class="vs-button" data-bs-dismiss="modal">{{ __('Cancel') }}</button><button type="submit" class="vs-button vs-button-primary">{{ __('Save Slot') }}</button></div>
        </form>
    </div></div>
</div>
<div class="modal fade" id="copyModal" tabindex="-1" aria-labelledby="copyTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form method="POST" action="{{ route('vet.availability.copyWeek') }}">
        @csrf
        <div class="modal-header"><h2 class="modal-title fs-5" id="copyTitle">{{ __('Copy Week Schedule') }}</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button></div>
        <div class="modal-body"><p>{{ __('Copy one day’s time slots to all days without a schedule. Existing schedules will be kept.') }}</p><label for="copyDay" class="form-label">{{ __('Copy from') }}</label><select class="form-select" name="source_day" id="copyDay" required><option value="">{{ __('Select a day') }}</option>@foreach($days as $day) @if($availability->get($day, collect())->isNotEmpty())<option value="{{ $day }}">{{ __(ucfirst($day)) }}</option>@endif @endforeach</select></div>
        <div class="modal-footer"><button type="button" class="vs-button" data-bs-dismiss="modal">{{ __('Cancel') }}</button><button class="vs-button vs-button-primary" type="submit">{{ __('Copy Schedule') }}</button></div>
    </form></div></div>
</div>
@endsection
@push('scripts')
<script>
function openSlotModal(day = 'monday', id = null, start = '09:00', end = '17:00', available = 1) {
    const form = document.getElementById('slotForm');
    form.action = id ? @json(url('/vet/availability')) + '/' + id : @json(route('vet.availability.store'));
    document.getElementById('formMethod').value = id ? 'PUT' : 'POST';
    document.getElementById('slotId').value = id || '';
    document.getElementById('slotDay').value = day;
    document.getElementById('slotStart').value = start;
    document.getElementById('slotEnd').value = end;
    document.getElementById('slotStatus').value = String(available);
    document.getElementById('slotModalTitle').textContent = id ? @json(__('Edit Slot')) : @json(__('Add New Slot'));
    document.getElementById('timeError').hidden = true;
    const show = () => bootstrap.Modal.getOrCreateInstance(document.getElementById('slotModal')).show();
    const current = document.querySelector('.modal.show');
    if (current) { current.addEventListener('hidden.bs.modal', show, {once:true}); bootstrap.Modal.getOrCreateInstance(current).hide(); } else { show(); }
}
document.getElementById('slotForm').addEventListener('submit', event => {
    const minutes = value => { const [h,m] = value.split(':').map(Number); return h*60+m; };
    const duration = minutes(document.getElementById('slotEnd').value) - minutes(document.getElementById('slotStart').value);
    if (!Number.isFinite(duration) || duration < 15) { event.preventDefault(); document.getElementById('timeError').hidden = false; }
});
@if(old('day_of_week'))
document.addEventListener('DOMContentLoaded', () => openSlotModal(@json(old('day_of_week')), @json(old('slot_id')), @json(old('start_time')), @json(old('end_time')), @json(old('is_available', 1))));
@endif
</script>
@endpush
