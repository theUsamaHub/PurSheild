@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .pc-main.vet-schedule { min-width:0; background:linear-gradient(120deg,#fbfdff,#fff 65%); font-family:'Roboto',Arial,sans-serif; --vs-ink:#0b1023; --vs-blue:#305795; --vs-muted:#7389ac; --vs-line:#e4ecf8; --vs-green:#006b55; color:var(--vs-blue); }
    .vet-schedule .pc-content { padding:20px 28px 40px; }
    .vet-schedule .pc-topbar { background:#f5f9fe; border-color:var(--vs-line); padding:18px 28px; }
    .vet-schedule .pc-search { max-width:400px; }
    .vet-schedule .pc-search input { border-color:#93a4c5; background:#fff; color:var(--vs-blue); border-radius:10px; padding:13px 66px 13px 48px; font-size:16px; }
    .vet-schedule .pc-search input::placeholder { color:#5e7aac; }
    .vet-schedule .pc-search i { left:20px; font-size:20px; color:var(--vs-blue); }
    .vet-schedule .pc-search kbd { color:#5e7aac; border-color:#dce7f8; }
    .vet-schedule .pc-user-name { color:var(--vs-ink); font-size:16px; }
    .vet-schedule .pc-user-role { color:var(--vs-blue); font-size:13px; margin-top:4px; }
    .vet-schedule .pc-user-avatar { width:48px; height:48px; }
    .vet-schedule .pc-bell { color:var(--vs-blue); font-size:25px; margin-right:16px; }
    .vet-schedule .pc-topbar-right > .dropdown { border-left:1px solid var(--vs-line); padding-left:20px; }
    .vs-heading { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:12px; }
    .vs-heading h1 { color:var(--vs-ink); font-size:clamp(28px,2.2vw,36px); font-weight:700; letter-spacing:-.8px; line-height:1.2; margin:0 0 4px; }
    .vs-heading p { font-size:clamp(14px,1.25vw,20px); margin:0; line-height:1.5; }
    .vs-button { display:inline-flex; justify-content:center; align-items:center; gap:12px; min-height:42px; padding:8px 18px; border:1px solid #dce7f8; border-radius:8px; background:#fff; color:var(--vs-blue); font:inherit; text-decoration:none; cursor:pointer; }
    .vs-button:hover { background:#f0f6ff; color:#21467e; }
    .vs-button-primary { background:linear-gradient(110deg,#00664f,#08725b); border-color:#00664f; color:#fff; font-weight:500; }
    .vs-button-primary:hover { background:#005842; color:#fff; }
    .vs-add { min-height:56px; min-width:236px; font-size:18px; border-radius:11px; }
    .vs-add i { font-size:28px; line-height:1; }
    .vs-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; margin-bottom:20px; }
    .vs-stat { position:relative; display:flex; align-items:center; gap:clamp(16px,2vw,32px); min-height:clamp(104px,7.35vw,123px); padding:16px 20px; border-radius:12px; background:#edfcf8; }
    .vs-stat-blue { background:#edf5ff; }
    .vs-stat-orange { background:#fff8ef; }
    .vs-stat-icon { flex-shrink:0; width:clamp(58px,4.6vw,77px); height:clamp(58px,4.6vw,77px); border-radius:50%; background:#d0faeb; display:grid; place-items:center; color:#00975e; font-size:clamp(30px,2.5vw,42px); }
    .vs-stat-blue .vs-stat-icon { background:#d6eaff; color:#087cff; }
    .vs-stat-orange .vs-stat-icon { background:#ffecd4; color:#ff8800; }
    .vs-stat strong { display:block; font-size:clamp(34px,2.6vw,44px); font-weight:700; line-height:1.15; color:var(--vs-ink); }
    .vs-stat-label { font-size:clamp(14px,1.25vw,20px); line-height:1.3; margin-top:4px; white-space:nowrap; }
    .vs-stat-chevron { position:absolute; right:20px; top:26px; display:grid; place-items:center; width:36px; height:36px; border:1px solid #d8e6fa; border-radius:50%; background:#ffffff70; }
    .vs-panel { padding:12px 20px 8px; border:1px solid var(--vs-line); border-radius:10px; background:#ffffffb8; }
    .vs-panel-heading { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:10px; }
    .vs-panel-heading h2 { display:flex; align-items:center; gap:14px; margin:0; color:var(--vs-ink); font-weight:700; font-size:clamp(21px,1.65vw,28px); letter-spacing:-.5px; }
    .vs-panel-heading h2 i { font-size:1.1em; }
    .vs-table-wrap { overflow-x:auto; }
    .vs-table { width:100%; border-collapse:separate; border-spacing:0; color:var(--vs-blue); font-size:14px; }
    .vs-table th { background:#f0f5fd; color:var(--vs-ink); font-weight:600; padding:9px 14px; white-space:nowrap; }
    .vs-table th:first-child { border-radius:7px 0 0 0; }
    .vs-table th:last-child { border-radius:0 7px 0 0; }
    .vs-table td { padding:7px 14px; border-bottom:1px solid var(--vs-line); vertical-align:middle; }
    .vs-table tbody tr:last-child td { border-bottom:0; }
    .vs-table tbody tr:hover { background:#fafdff; }
    .vs-table strong { color:var(--vs-ink); font-weight:500; }
    .vs-table small { display:block; color:var(--vs-muted); font-size:12px; line-height:1.35; }
    .vs-week-table { min-width:740px; font-size:16px; }
    .vs-week-table th { padding:10px 16px; line-height:1.4; }
    .vs-week-table td { padding:8px 16px; height:clamp(54px,3.6vw,61px); }
    .vs-week-table th:first-child { width:17%; }
    .vs-week-table th:nth-child(2) { width:19%; }
    .vs-week-table th:last-child { width:90px; text-align:center; }
    .vs-week-table small { font-size:14px; color:#5476ac; }
    .vs-week-table strong { display:block; font-size:18px; line-height:1.25; }
    .vs-status { display:inline-flex; align-items:center; gap:8px; border-radius:8px; padding:5px 10px; font-size:12px; line-height:1.5; white-space:nowrap; color:#008b5b; background:#e2faf1; }
    .vs-status::before { content:''; width:7px; height:7px; border-radius:50%; background:currentColor; flex-shrink:0; }
    .vs-status-pending { color:#a86b00; background:#fff5d6; }
    .vs-status-pending::before { background:#eda400; }
    .vs-status-rescheduled { color:#0675e8; background:#e0efff; }
    .vs-status-cancelled,.vs-status-rejected,.vs-status-unavailable { color:#ec202e; background:#ffebee; }
    .vs-week-table .vs-status { font-size:18px; padding:7px 16px; gap:12px; background:#e9fbf5; }
    .vs-week-table .vs-status-unavailable { background:#ffebee; }
    .vs-week-table .vs-status::before { width:11px; height:11px; }
    .vs-slots { display:flex; flex-wrap:wrap; gap:10px 16px; }
    .vs-slot { background:#f0f5fd; color:var(--vs-blue); padding:6px 20px; border-radius:8px; white-space:nowrap; font-size:15px; }
    .vs-icon-button { display:inline-flex; justify-content:center; align-items:center; width:32px; height:32px; border:1px solid var(--vs-line); border-radius:7px; background:#fcfdff; color:var(--vs-blue); text-decoration:none; padding:0; font-size:15px; flex-shrink:0; }
    .vs-icon-button:hover { background:#eaf3ff; color:#174584; border-color:#b9d0f0; }
    .vs-icon-button:disabled { opacity:.4; cursor:default; }
    .vs-edit { width:48px; height:41px; background:#f2f6ff; font-size:21px; border-color:#d9e6fb; }
    .vs-filters { display:grid; grid-template-columns:minmax(240px,2.2fr) minmax(150px,1fr) minmax(140px,1fr) minmax(140px,.9fr) auto; gap:12px; margin-bottom:16px; padding:12px; border:1px solid var(--vs-line); background:#fbfdff; border-radius:9px; }
    .vs-field { position:relative; min-width:0; }
    .vs-field > i { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--vs-blue); font-size:17px; pointer-events:none; }
    .vs-filters input,.vs-filters select { min-height:42px; width:100%; border:1px solid #e0e7f2; border-radius:6px; padding:8px 12px; background-color:#fff; color:#243956; font:inherit; font-size:14px; }
    .vs-field input { padding-left:42px; }
    .vs-filters input::placeholder { color:#7b90b1; }
    .vs-date-placeholder { position:absolute; left:42px; top:50%; transform:translateY(-50%); color:#7b90b1; pointer-events:none; background:white; font-size:14px; }
    .vs-field .vs-date-empty:not(:focus)::-webkit-datetime-edit { color:transparent; }
    .vs-field input[type=date]::-webkit-calendar-picker-indicator { position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; }
    .vs-field input:focus + .vs-date-placeholder { display:none; }
    .vs-pagination { display:flex; align-items:center; gap:8px; }
    .vs-pagination span { margin-right:10px; font-size:13px; color:var(--vs-muted); }
    .vs-appointments-table { min-width:880px; font-size:12px; }
    .vs-appointments-table th,.vs-appointments-table td { padding:5px 12px; }
    .vs-appointments-table td { height:39px; padding-top:3px; padding-bottom:3px; }
    .vs-appointments-table strong { display:block; line-height:1.2; }
    .vs-appointments-table small { line-height:1.2; }
    .vs-appointments-table td:nth-child(1),.vs-appointments-table td:nth-child(2) { white-space:nowrap; }
    .vs-pet { display:flex; align-items:center; gap:12px; min-width:140px; }
    .vs-pet-avatar { width:var(--avatar-size,32px); height:var(--avatar-size,32px); border-radius:50%; object-fit:cover; flex-shrink:0; background:#e1f4eb; color:#007558; display:grid; place-items:center; font-weight:600; }
    .vet-schedule [hidden] { display:none!important; }
    .vs-reason { max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .vs-actions { display:flex; gap:7px; }
    .vs-empty { text-align:center; padding:54px 16px; color:var(--vs-muted); }
    .vs-empty > i { font-size:36px; display:block; margin-bottom:12px; }
    .vet-schedule .modal-content { border:1px solid var(--vs-line); border-radius:14px; color:var(--vs-blue); box-shadow:0 20px 80px #102c4926; }
    .vet-schedule .modal-header,.vet-schedule .modal-footer { border-color:var(--vs-line); }
    .vet-schedule .modal-title { color:var(--vs-ink); font-weight:600; }
    .vet-schedule .form-control,.vet-schedule .form-select { border-color:#dce6f3; }
    .vs-editor-slot { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:12px 0; border-bottom:1px solid var(--vs-line); }
    .vs-editor-slot:last-child { border:0; }
    .vs-detail .card { border:1px solid var(--vs-line); box-shadow:none; border-radius:10px; }
    .vs-detail .card-header { padding:16px 20px; border-color:var(--vs-line); border-radius:10px 10px 0 0; }
    .vs-detail .card-body { padding:20px; }
    .vs-detail .text-muted { color:var(--vs-muted)!important; }
    .vs-detail .btn-primary { background:var(--vs-green); border-color:var(--vs-green); }
    .vs-detail .btn-outline-primary { color:var(--vs-blue); border-color:#d4e2f5; }
    .vet-schedule :is(button,a,input,select):focus-visible { outline:3px solid #80b9f4; outline-offset:3px; box-shadow:none; }
    @media(min-width:1500px) { .vs-appointments-table { font-size:14px; } .vs-appointments-table td { height:52px; } .vs-appointments-table .vs-pet-avatar { width:38px; height:38px; } }
    @media(max-width:1499px) { .vet-schedule .pc-topbar { padding:14px 24px; } .vet-schedule .pc-search input { padding-top:8px; padding-bottom:8px; font-size:14px; } .vet-schedule .pc-user-avatar { width:40px; height:40px; } .vet-schedule .pc-content { padding-top:16px; } .vs-heading h1 { line-height:1.1; } .vs-heading p { font-size:14px; } }
    @media(max-width:1250px) { .vs-stat-label { white-space:normal; } .vs-filters { grid-template-columns:2fr 1fr 1fr; } .vs-stat { gap:14px; padding:16px; } .vs-stat-chevron { display:none; } }
    @media(max-width:767px) { .vet-schedule .pc-content { padding:20px 16px; } .vet-schedule .pc-topbar { padding:12px 16px; } .vet-schedule .pc-user > div:not(.pc-user-avatar),.vet-schedule .pc-search kbd { display:none; } .vet-schedule .pc-search input { padding-right:12px; } .vet-schedule .pc-topbar-right { gap:4px; } .vet-schedule .pc-topbar-right > .dropdown { padding-left:10px; } .vet-schedule .pc-bell { margin-right:8px; } .vs-heading { align-items:flex-start; flex-wrap:wrap; } .vs-add { min-width:0; min-height:44px; font-size:15px; } .vs-stats { grid-template-columns:1fr; gap:10px; } .vs-stat { min-height:88px; } .vs-stat strong { font-size:30px; } .vs-stat-label { font-size:15px; } .vs-stat-chevron { display:grid; } .vs-stat-icon { width:54px; height:54px; } .vs-panel { padding:12px; } .vs-panel-heading { flex-wrap:wrap; } .vs-panel-heading h2 { font-size:21px; } .vs-filters { grid-template-columns:1fr 1fr; } .vs-filters .vs-field:first-child { grid-column:1/-1; } .vs-pagination { margin-left:auto; } }
    @media(prefers-reduced-motion:reduce) { .vet-schedule * { scroll-behavior:auto!important; transition:none!important; animation:none!important; } }
</style>
@endpush
