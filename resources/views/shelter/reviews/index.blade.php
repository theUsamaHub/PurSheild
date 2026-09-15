@extends('layouts.shelter.app')

@section('title','My Reviews')
@section('content')
<div class="shr-review-page fade-in">
    {{-- Page heading --}}
    <div class="shr-page-heading">
        <h1>{{ __('My Reviews') }}</h1><span class="float-end small text-muted">{{ number_format($avgRating ?? 0, 1) }} / 5 · {{ $totalReviews }} reviews</span>
        <p>{{ __('View, manage and respond to adopter feedback.') }}</p>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('shelter.reviews.index') }}" class="shr-review-filters" id="reviewFilterForm">
        <div class="shr-filter shr-filter-search">
            <i class="bi bi-search"></i>
            <input
                type="search"
                maxlength="200"
                name="search"
                value="{{ request('search') }}"
                placeholder="{{ __('Search by owner, animal, review or reply...') }}"
                aria-label="{{ __('Search reviews') }}"
            >
        </div>

        <div class="shr-filter shr-filter-select">
            <i class="bi bi-star-fill"></i>
            <select name="rating" aria-label="{{ __('Filter by rating') }}" onchange="this.form.requestSubmit()">
                <option value="">{{ __('All Ratings') }}</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                        {{ $i }} {{ __('Star') }}{{ $i > 1 ? 's' : '' }}
                    </option>
                @endfor
            </select>
            <i class="bi bi-chevron-down shr-select-arrow"></i>
        </div>

        <div class="shr-filter shr-filter-select">
            <i class="bi bi-calendar3"></i>
            <select name="sort" aria-label="{{ __('Sort reviews') }}" onchange="this.form.requestSubmit()">
                <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>{{ __('Latest First') }}</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>{{ __('Oldest First') }}</option>
                <option value="highest" {{ request('sort') === 'highest' ? 'selected' : '' }}>{{ __('Highest Rating') }}</option>
                <option value="lowest" {{ request('sort') === 'lowest' ? 'selected' : '' }}>{{ __('Lowest Rating') }}</option>
            </select>
            <i class="bi bi-chevron-down shr-select-arrow"></i>
        </div>

        <div class="shr-filter shr-filter-select"><select name="response" aria-label="{{ __('Filter by response') }}" onchange="this.form.requestSubmit()">
            @foreach(['all' => 'All responses', 'unanswered' => 'Needs reply', 'replied' => 'Replied'] as $value => $label)
            <option value="{{ $value }}" @selected(request('response', 'all') === $value)>{{ __($label) }}</option>
            @endforeach
        </select></div>
        <div class="d-flex gap-2"><button type="submit" class="shr-reset-btn">{{ __('Apply') }}</button>
        <a href="{{ route('shelter.reviews.index') }}" class="shr-reset-btn" title="{{ __('Reset') }}">
            <i class="bi bi-arrow-counterclockwise"></i>
            <span>{{ __('Reset') }}</span>
        </a></div>
    </form>

    {{-- Reviews Table --}}
    <div class="shr-reviews-card">
        <div class="shr-reviews-card-header">
            <h2><i class="bi bi-clipboard2-check"></i>{{ __('Shelter Reviews') }}</h2>
            <span class="shr-reviews-count">
                {{ __('Showing :from of :total reviews', [
                    'from' => $reviews->count(),
                    'total' => method_exists($reviews, 'total') ? $reviews->total() : $reviews->count()
                ]) }}
            </span>
        </div>

        @if($reviews->count())
            <div class="table-responsive shr-table-wrap">
                <table class="shr-reviews-table">
                    <thead>
                        <tr>
                            <th>{{ __('Owner') }}</th>
                            <th>{{ __('Rating') }}</th>
                            <th>{{ __('Review') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                            @php
                                $statusMap = [
                                    'published' => ['label' => __('Published'), 'color' => '#0f9f5f', 'bg' => '#dff8e9'],
                                    'pending'   => ['label' => __('Pending Reply'), 'color' => '#f3a400', 'bg' => '#fff4d7'],
                                    'resolved'  => ['label' => __('Replied'), 'color' => '#1676e5', 'bg' => '#e3f0ff'],
                                ];

                                $status = $review->replied_at ? $statusMap['resolved'] : $statusMap['pending'];
                                $ownerName = $review->user->name ?? __('Anonymous');
                                $comment = $review->comment ?? '';
                            @endphp
                            <tr>
                                <td class="shr-owner-name">{{ $ownerName }}<small class="d-block text-muted mt-1">{{ $review->reviewable?->pet_name }}</small></td>

                                <td>
                                    <div class="shr-stars" aria-label="{{ $review->rating }} {{ __('out of 5 stars') }}">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '-fill shr-star-empty' }}"></i>
                                        @endfor
                                    </div>
                                </td>

                                <td class="shr-review-text">
                                    <span class="shr-clamp-2">
                                        @if($comment)
                                            &ldquo;{{ $comment }}&rdquo;
                                        @else
                                            <span class="shr-muted">{{ __('No comment') }}</span>
                                        @endif
                                    </span>
                                </td>

                                <td class="shr-date">{{ $review->created_at->format('d M Y') }}</td>

                                <td>
                                    <span class="shr-status-pill" style="color:{{ $status['color'] }};background:{{ $status['bg'] }};">
                                        <i class="bi bi-circle-fill"></i>
                                        {{ $status['label'] }}
                                    </span>
                                </td>

                                <td>
                                    <div class="shr-actions">
                                        <button
                                            type="button"
                                            class="shr-action-btn"
                                            title="{{ __('View') }}" aria-label="View review by {{ $ownerName }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#reviewDetailModal"
                                            data-id="{{ $review->id }}"
                                            data-url="{{ route('shelter.reviews.reply', $review) }}"
                                            data-pet="{{ $review->reviewable?->pet_name }}"
                                            data-owner="{{ $ownerName }}"
                                            data-rating="{{ $review->rating }}"
                                            data-comment="{{ $comment ?: __('No comment') }}"
                                            data-reply="{{ $review->reply ?? '' }}"
                                            data-reply-date="{{ $review->replied_at ? $review->replied_at->format('d M Y, g:i A') : '' }}"
                                            data-date="{{ $review->created_at->format('d M Y') }}"
                                            data-status="{{ $status['label'] }}"
                                            data-status-color="{{ $status['color'] }}"
                                            data-status-bg="{{ $status['bg'] }}"
                                            data-avatar=""
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button
                                            type="button"
                                            class="shr-action-btn"
                                            title="{{ __('Reply') }}" aria-label="Reply to {{ $ownerName }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#reviewReplyModal"
                                            data-id="{{ $review->id }}"
                                            data-url="{{ route('shelter.reviews.reply', $review) }}"
                                            data-pet="{{ $review->reviewable?->pet_name }}"
                                            data-owner="{{ $ownerName }}"
                                            data-reply="{{ $review->reply ?? '' }}"
                                        >
                                            <i class="bi bi-chat-square-text"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="shr-empty-state">
                <i class="bi bi-star"></i>
                <p>{{ __('No reviews match your filters.') }}</p>
            </div>
        @endif

        @if($reviews->hasPages())
            <div class="shr-pagination-wrap">
                @include('shelter.partials.pagination',['items'=>$reviews,'noun'=>'reviews'])
            </div>
        @endif
    </div>
</div>

{{-- Review Detail Modal --}}
<div class="modal fade" id="reviewDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shr-review-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">{{ __('Review Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>

            <div class="modal-body pt-2">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div id="modalAvatarWrap"></div>
                    <div>
                        <div class="fw-bold" id="modalPetName"></div>
                        <div class="text-muted" id="modalOwnerName"></div>
                    </div>
                    <span class="shr-status-pill ms-auto" id="modalStatusPill">
                        <i class="bi bi-circle-fill"></i>
                        <span id="modalStatusLabel"></span>
                    </span>
                </div>

                <div class="shr-modal-stars mb-3" id="modalStars"></div>
                <div class="shr-modal-comment mb-3" id="modalComment"></div>

                <div id="modalReplySection" class="mb-3 d-none">
                    <div class="p-3 bg-light rounded border border-success-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-success small"><i class="bi bi-reply-fill me-1"></i>{{ __('Your Reply') }}</span>
                            <span class="text-muted small" id="modalReplyDate"></span>
                        </div>
                        <p class="mb-0 text-secondary small" id="modalReplyText"></p>
                    </div>
                </div>

                <div class="shr-modal-date">
                    <i class="bi bi-calendar3"></i>
                    <span id="modalDate"></span>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn btn-primary" id="modalOpenReplyBtn" style="background-color: var(--shr-primary); border-color: var(--shr-primary);">{{ __('Reply') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Review Reply Modal --}}
<div class="modal fade" id="reviewReplyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shr-review-modal">
            <form id="reviewReplyForm" method="POST">
                @csrf
                <input type="hidden" name="review_id" id="replyReviewId">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">{{ __('Respond to Review') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>

                <div class="modal-body pt-2">
                    <p class="text-muted mb-3" id="replyTargetText"></p>
                    <div class="mb-3">
                        <label for="replyInput" class="form-label fw-semibold">{{ __('Your Response') }}</label>
                        <textarea id="replyInput" name="reply" class="form-control" rows="4" placeholder="{{ __('Write your response to the pet owner...') }}" required maxlength="2000"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" style="background-color: var(--shr-primary); border-color: var(--shr-primary);">{{ __('Submit Reply') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .shr-review-page,
    .shr-review-page * {
        box-sizing: border-box;
    }

    .shr-review-page, .shr-review-modal {
        --shr-primary: var(--sh-green);
        --shr-text: #101827;
        --shr-body: #303744;
        --shr-muted: #667085;
        --shr-border: #dbe3ec;
        --shr-border-soft: #e7edf3;
        --shr-head-bg: #f3f6f9;
        --shr-star: #ffa800;
        width: 100%;
        color: var(--shr-text);
        font-family: Roboto, Arial, sans-serif;
    }

    .shr-page-heading {
        margin: 0 0 13px;
    }

    .shr-page-heading h1 {
        margin: 0 0 2px;
        color: #0f1722;
        font-size: 26px;
        line-height: 1.18;
        font-weight: 750;
        letter-spacing: -0.55px;
    }

    .shr-page-heading p {
        margin: 0;
        color: #343b47;
        font-size: 14px;
        line-height: 1.4;
        font-weight: 400;
    }

    .shr-review-filters {
        display: grid;
        grid-template-columns: minmax(200px, 2fr) repeat(3, minmax(130px, 1fr)) auto;
        gap: 12px;
        width: 100%;
        padding: 13px 14px;
        margin-bottom: 17px;
        background: #fff;
        border: 1px solid var(--shr-border);
        border-radius: 9px;
    }

    .shr-filter {
        min-width: 0;
        height: 42px;
        display: flex;
        align-items: center;
        position: relative;
        background: #fff;
        border: 1px solid #d8e0e9;
        border-radius: 8px;
    }

    .shr-filter > i:first-child {
        width: 48px;
        flex: 0 0 48px;
        display: grid;
        place-items: center;
        color: #111c28;
        font-size: 18px;
    }

    .shr-filter-search input,
    .shr-filter-select select {
        width: 100%;
        min-width: 0;
        height: 100%;
        margin: 0;
        padding: 0 14px 0 0;
        border: 0;
        outline: 0;
        background: transparent;
        box-shadow: none;
        color: #202833;
        font-family: inherit;
        font-size: 13px;
        font-weight: 400;
    }

    .shr-filter-search input::placeholder {
        color: #6f7886;
        opacity: 1;
    }

    .shr-filter-select select {
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
        padding-right: 40px;
        font-weight: 500;
    }

    .shr-filter-select .shr-select-arrow {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #111c28;
        font-size: 13px;
        pointer-events: none;
    }

    .shr-reset-btn {
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 0 18px;
        color: #1c2430;
        background: #fff;
        border: 1px solid #d8e0e9;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: background-color .15s ease, border-color .15s ease;
    }

    .shr-reset-btn:hover {
        color: #1c2430;
        background: #f8fafc;
        border-color: #cbd5df;
    }

    .shr-reset-btn i {
        font-size: 18px;
    }

    .shr-reviews-card {
        width: 100%;
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--shr-border);
        border-radius: 10px;
    }

    .shr-reviews-card-header {
        min-height: 56px;
        padding: 0 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 0;
    }

    .shr-reviews-card-header h2 {
        margin: 0;
        display: inline-flex;
        align-items: center;
        gap: 11px;
        color: #111827;
        font-size: 18px;
        line-height: 1;
        font-weight: 750;
        letter-spacing: -0.28px;
    }

    .shr-reviews-card-header h2 i {
        color: #111c28;
        font-size: 25px;
        font-weight: 400;
    }

    .shr-reviews-count {
        color: #4b5563;
        font-size: 14px;
        font-weight: 400;
        white-space: nowrap;
    }

    .shr-table-wrap {
        padding: 0 13px 13px;
    }

    .shr-reviews-table {
        width: 100%;
        min-width: 850px;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
        color: #252d39;
    }

    .shr-reviews-table th,
    .shr-reviews-table td {
        border-right: 1px solid var(--shr-border-soft);
        border-bottom: 1px solid var(--shr-border-soft);
        text-align: left;
        vertical-align: middle;
    }

    .shr-reviews-table th:first-child,
    .shr-reviews-table td:first-child {
        border-left: 1px solid var(--shr-border-soft);
    }

    .shr-reviews-table thead th {
        height: 34px;
        padding: 7px 12px;
        background: var(--shr-head-bg);
        color: #202733;
        font-size: 12px;
        line-height: 1.1;
        font-weight: 700;
        white-space: nowrap;
    }

    .shr-reviews-table thead th:first-child { border-top-left-radius: 6px; }
    .shr-reviews-table thead th:last-child { border-top-right-radius: 6px; }

    .shr-reviews-table tbody td {
        height: 58px;
        padding: 6px 12px;
        background: #fff;
        color: #303744;
        font-size: 13px;
        line-height: 1.28;
    }

    .shr-reviews-table tbody tr:hover td {
        background: #fbfcfd;
    }

    .shr-reviews-table th:nth-child(1) { width: 17%; }
    .shr-reviews-table th:nth-child(2) { width: 12%; }
    .shr-reviews-table th:nth-child(3) { width: 30%; }
    .shr-reviews-table th:nth-child(4) { width: 14%; }
    .shr-reviews-table th:nth-child(5) { width: 14%; }
    .shr-reviews-table th:nth-child(6) { width: 13%; }

    .shr-pet-cell {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 0;
    }

    .shr-avatar {
        width: 39px;
        height: 39px;
        flex: 0 0 39px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #e3e8ee;
        overflow: hidden;
    }

    .shr-avatar-fallback {
        display: grid;
        place-items: center;
        background: #edf3ef;
        color: #0f6b4e;
        font-size: 14px;
        font-weight: 700;
    }

    .shr-pet-name {
        overflow: hidden;
        color: #111827;
        font-size: 14px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .shr-owner-name,
    .shr-date {
        color: #343b47 !important;
        font-size: 13px !important;
        font-weight: 400;
        white-space: nowrap;
    }

    .shr-stars {
        display: inline-flex;
        align-items: center;
        gap: 2px;
        color: var(--shr-star);
        white-space: nowrap;
        font-size: 16px;
        line-height: 1;
    }

    .shr-star-empty {
        color: #d4dde6;
    }

    .shr-review-text {
        color: #343b47 !important;
        font-size: 13px !important;
    }

    .shr-clamp-2 {
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        line-clamp: 2;
    }

    .shr-muted {
        color: #8a94a2;
    }

    .shr-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 34px;
        padding: 6px 8px;
        border-radius: 8px;
        font-size: 12px;
        line-height: 1.25;
        font-weight: 500;
        white-space: normal;
    }

    .shr-status-pill > i {
        font-size: 9px;
    }

    .shr-actions {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 8px;
        white-space: nowrap;
    }

    .shr-action-btn {
        width: 38px;
        height: 38px;
        display: inline-grid;
        place-items: center;
        padding: 0;
        color: #1f2937;
        background: #fff;
        border: 1px solid #dbe3ec;
        border-radius: 8px;
        outline: 0;
        box-shadow: none;
        text-decoration: none;
        cursor: pointer;
        transition: background-color .15s ease, border-color .15s ease, color .15s ease;
    }

    .shr-action-btn i {
        font-size: 18px;
        line-height: 1;
    }

    .shr-action-btn:hover {
        color: #111827;
        background: #f6f8fa;
        border-color: #cbd5df;
    }

    .shr-empty-state {
        padding: 48px 20px 55px;
        text-align: center;
        color: #7b8491;
    }

    .shr-empty-state i {
        display: block;
        margin-bottom: 10px;
        font-size: 34px;
    }

    .shr-empty-state p {
        margin: 0;
        font-size: 14px;
    }

    .shr-pagination-wrap {
        padding: 13px 16px;
        border-top: 1px solid var(--shr-border-soft);
    }

    /* Modal kept functional, styled to the same visual language */
    .shr-review-modal {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, .15);
        font-family: Roboto, Arial, sans-serif;
    }

    #modalAvatarWrap .shr-avatar {
        width: 48px;
        height: 42px;
    }

    #modalPetName {
        color: #111827;
        font-size: 13px;
    }

    #modalOwnerName {
        font-size: 13px;
    }

    .shr-modal-stars {
        color: var(--shr-star);
        font-size: 18px;
    }

    .shr-modal-comment {
        padding: 14px 16px;
        color: #374151;
        background: #f8fafc;
        border-radius: 9px;
        font-size: 14px;
        font-style: italic;
    }

    .shr-modal-date {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #6b7280;
        font-size: 13px;
    }

    @media (max-width: 1400px) {
        .shr-review-filters {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 992px) {
        .shr-review-filters {
            grid-template-columns: 1fr 1fr;
        }

        .shr-filter-search {
            grid-column: 1 / -1;
        }

        .shr-reset-btn {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .shr-page-heading h1 {
            font-size: 25px;
        }

        .shr-page-heading p {
            font-size: 13px;
        }

        .shr-review-filters {
            grid-template-columns: 1fr;
            padding: 10px;
        }

        .shr-filter-search {
            grid-column: auto;
        }

        .shr-reviews-card-header {
            align-items: flex-start;
            flex-direction: column;
            padding: 16px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('#reviewFilterForm input[name=search]').addEventListener('search', event => event.target.form.requestSubmit());
        @if(old('review_id'))
        const retryButton = Array.from(document.querySelectorAll('[data-bs-target="#reviewReplyModal"]')).find(button => button.dataset.id === String(@json(old('review_id'))));
        if (retryButton) {
            document.getElementById('reviewReplyForm').action = retryButton.dataset.url;
            document.getElementById('replyReviewId').value = retryButton.dataset.id;
            document.getElementById('replyInput').value = @json(old('reply'));
            document.getElementById('replyTargetText').textContent = 'Replying to ' + retryButton.dataset.owner;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('reviewReplyModal')).show();
        }
        @endif
        const detailModal = document.getElementById('reviewDetailModal');
        if (detailModal) {
            detailModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                if (!btn) return;

                const pet = btn.getAttribute('data-pet') || '-';
                const owner = btn.getAttribute('data-owner') || '';
                const rating = parseInt(btn.getAttribute('data-rating'), 10) || 0;
                const comment = btn.getAttribute('data-comment') || '';
                const reply = btn.getAttribute('data-reply') || '';
                const replyDate = btn.getAttribute('data-reply-date') || '';
                const date = btn.getAttribute('data-date') || '';
                const statusLabel = btn.getAttribute('data-status') || '';
                const statusColor = btn.getAttribute('data-status-color') || '#0f9f5f';
                const statusBg = btn.getAttribute('data-status-bg') || '#dff8e9';
                const avatar = btn.getAttribute('data-avatar') || '';
                const replyUrl = btn.getAttribute('data-url') || '';

                document.getElementById('modalPetName').textContent = pet === '-' ? 'Adopter feedback' : pet;
                document.getElementById('modalOwnerName').textContent = owner;
                document.getElementById('modalComment').textContent = '“' + comment + '”';
                document.getElementById('modalDate').textContent = date;
                document.getElementById('modalStatusLabel').textContent = statusLabel;

                const pill = document.getElementById('modalStatusPill');
                pill.style.color = statusColor;
                pill.style.background = statusBg;

                const replySection = document.getElementById('modalReplySection');
                if (reply) {
                    replySection.classList.remove('d-none');
                    document.getElementById('modalReplyText').textContent = reply;
                    document.getElementById('modalReplyDate').textContent = replyDate;
                } else {
                    replySection.classList.add('d-none');
                }

                const openReplyBtn = document.getElementById('modalOpenReplyBtn');
                if (openReplyBtn) {
                    openReplyBtn.onclick = function () {
                        document.getElementById('replyReviewId').value = btn.dataset.id;
                        detailModal.addEventListener('hidden.bs.modal', () => {
                        const replyModalEl = document.getElementById('reviewReplyModal');
                        const replyModal = bootstrap.Modal.getOrCreateInstance(replyModalEl);

                        document.getElementById('reviewReplyForm').action = replyUrl;
                        document.getElementById('replyTargetText').textContent = 'Replying to ' + owner;
                        document.getElementById('replyInput').value = reply;
                        replyModal.show();
                        }, { once: true });
                        bootstrap.Modal.getInstance(detailModal).hide();
                    };
                }

                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    const emptyClass = i <= rating ? '' : ' shr-star-empty';
                    stars += '<i class="bi bi-star-fill' + emptyClass + '"></i>';
                }
                document.getElementById('modalStars').innerHTML = stars;

                const avatarWrap = document.getElementById('modalAvatarWrap');
                if (avatar) {
                    const img = document.createElement('img');
                    img.src = avatar;
                    img.className = 'shr-avatar';
                    img.alt = pet;
                    avatarWrap.innerHTML = '';
                    avatarWrap.appendChild(img);
                } else {
                    const fallback = document.createElement('div');
                    fallback.className = 'shr-avatar shr-avatar-fallback';
                    fallback.textContent = owner ? owner.charAt(0).toUpperCase() : '?';
                    avatarWrap.innerHTML = '';
                    avatarWrap.appendChild(fallback);
                }
            });
        }

        const replyModal = document.getElementById('reviewReplyModal');
        if (replyModal) {
            replyModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                if (!btn) return;

                const url = btn.getAttribute('data-url') || '';
                const pet = btn.getAttribute('data-pet') || '';
                const owner = btn.getAttribute('data-owner') || '';
                const reply = btn.getAttribute('data-reply') || '';

                document.getElementById('replyReviewId').value = btn.dataset.id;
                if (url) {
                    document.getElementById('reviewReplyForm').action = url;
                }
                document.getElementById('replyTargetText').textContent = 'Replying to ' + owner;
                document.getElementById('replyInput').value = reply;
            });
        }
    });
</script>
@endpush
@endsection
