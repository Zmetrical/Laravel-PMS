@extends('layouts.main')

@section('title', 'Leave Management')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">Leave Management</li>
    </ol>
@endsection

@section('content')

{{-- ── Flash Messages ─────────────────────────────────────────────────── --}}
@if (session('success'))
    <div class="alert alert-light border border-secondary d-flex justify-content-between align-items-center mb-4" role="alert">
        <span class="font-weight-bold text-dark">{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-light border border-dark d-flex justify-content-between align-items-center mb-4" role="alert">
        <span class="font-weight-bold text-muted">{{ session('error') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-light border border-dark d-flex justify-content-between align-items-center mb-4" role="alert">
        <span class="font-weight-bold text-muted">{{ $errors->first() }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── Page Header ─────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Leave Management</h4>
        <small class="text-muted font-weight-bold text-uppercase">File leave requests and track your leave credits</small>
    </div>
    <button class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm" id="toggleFormBtn" onclick="toggleForm()">
        Apply for Leave
    </button>
</div>

{{-- ── Leave Balance Summary Cards ─────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    @foreach ($leaveTypes as $lt)
        @php
            $bal   = $balances->get($lt->id);
            $avail = $bal ? $bal->balance        : 0;
            $used  = $bal ? $bal->used_days      : 0;
            $total = $bal ? $bal->total_entitled : $lt->max_days_per_year;
        @endphp
        <div class="col-md-3">
            <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
                <span class="text-muted small font-weight-bold text-uppercase mb-2">{{ $lt->name }}</span>
                <span class="h3 font-weight-bold text-dark mb-1">{{ number_format($avail, 0) }} <span class="h6 text-muted">days</span></span>
                <span class="text-muted" style="font-size: 0.7rem;">Used: {{ $used }} <span class="mx-1">|</span> Total: {{ $total }}</span>
            </div>
        </div>
    @endforeach
</div>

{{-- ── Leave Application Form ───────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-5 {{ $errors->any() || old('leave_type_id') ? '' : 'd-none' }}" id="leaveFormCard">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">New Leave Application</h6>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('employee.leave.store') }}">
            @csrf
            {{-- Hidden fields populated by JS --}}
            <input type="hidden" name="start_date" id="inputStartDate" value="{{ old('start_date') }}">
            <input type="hidden" name="end_date"   id="inputEndDate"   value="{{ old('end_date') }}">

            <div class="row g-4">

                {{-- ── LEFT: Calendar ─────────────────────────────────── --}}
                <div class="col-lg-6">
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Select Leave Dates</label>

                    {{-- Month navigation --}}
                    <div class="d-flex align-items-center justify-content-between mb-3 border rounded px-3 py-2 bg-light">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="prevMonth()"><</button>
                        <span class="font-weight-bold text-dark text-uppercase" id="calMonthLabel"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="nextMonth()">></button>
                    </div>

                    {{-- Weekday headers --}}
                    <div class="row g-1 mb-2 border-bottom pb-2 text-center">
                        @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                            <div class="col text-muted small font-weight-bold text-uppercase">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Calendar grid --}}
                    <div id="calGrid"></div>

                    {{-- Selected range info --}}
                    <div class="mt-3" id="selectedRangeInfo"></div>

                    {{-- Legend --}}
                    <div class="d-flex flex-wrap align-items-center pt-3 mt-4 border-top text-muted small font-weight-bold text-uppercase gap-3">
                        <div class="d-flex align-items-center">
                            <span class="p-1 border border-secondary bg-secondary rounded-circle me-2"></span> Selected
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="p-1 border border-dark bg-light rounded-circle me-2"></span> In Range
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="p-1 border border-light bg-light rounded-circle me-2"></span> Filed / Past / Off
                        </div>
                    </div>
                </div>

                {{-- ── RIGHT: Leave Type + Balance + Reason ────────────── --}}
                <div class="col-lg-6">

                    {{-- Leave Type --}}
                    <div class="mb-4">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Leave Type</label>
                        <select name="leave_type_id" id="leaveTypeSelect" class="form-select border-secondary shadow-sm font-weight-bold @error('leave_type_id') is-invalid @enderror" onchange="onTypeChange()" required>
                            <option value="">— Select Leave Type —</option>
                            @foreach ($leaveTypes as $lt)
                                @php
                                    $bal   = $balances->get($lt->id);
                                    $avail = $bal ? $bal->balance        : 0;
                                    $used  = $bal ? $bal->used_days      : 0;
                                    $total = $bal ? $bal->total_entitled : $lt->max_days_per_year;
                                @endphp
                                <option value="{{ $lt->id }}"
                                        data-balance="{{ $avail }}"
                                        data-used="{{ $used }}"
                                        data-total="{{ $total }}"
                                        data-name="{{ $lt->name }}"
                                        data-desc="{{ $lt->description }}"
                                        {{ old('leave_type_id') == $lt->id ? 'selected' : '' }}>
                                    {{ $lt->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('leave_type_id')
                            <div class="invalid-feedback font-weight-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Balance info box --}}
                    <div class="mb-4">
                        <div class="border rounded p-3 bg-light text-center d-flex flex-column justify-content-center" style="min-height: 100px;" id="balanceInfoBox">
                            <span class="text-muted small font-weight-bold">Select a leave type to see your balance</span>
                        </div>
                    </div>

                    {{-- Leave Summary box --}}
                    <div class="mb-4 d-none" id="leaveSummaryBox">
                        <div class="border border-secondary rounded p-3 bg-white shadow-sm">
                            <p class="text-muted small font-weight-bold text-uppercase mb-3 text-center border-bottom pb-2">Leave Summary</p>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted font-weight-bold text-uppercase">Start Date</span>
                                <span class="font-weight-bold text-dark" id="summaryStart">—</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted font-weight-bold text-uppercase">End Date</span>
                                <span class="font-weight-bold text-dark" id="summaryEnd">—</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted font-weight-bold text-uppercase">Working Days</span>
                                <span class="font-weight-bold text-dark" id="summaryDays">—</span>
                            </div>
                            <div class="d-flex justify-content-between small mt-3 pt-2 border-top">
                                <span class="text-dark font-weight-bold text-uppercase">Remaining After</span>
                                <span class="font-weight-bold text-dark h5 mb-0" id="summaryRemaining">—</span>
                            </div>
                        </div>
                    </div>

                    {{-- Reason --}}
                    <div class="mb-4">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                            Reason <span class="text-danger">*</span>
                        </label>
                        <textarea name="reason" class="form-control border shadow-sm p-3 @error('reason') is-invalid @enderror" rows="4" placeholder="Briefly describe the reason for your leave..." required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback font-weight-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-secondary btn-lg flex-grow-1 font-weight-bold shadow-sm" id="submitLeaveBtn" disabled>
                            Submit Request
                        </button>
                        <button type="button" class="btn btn-outline-dark btn-lg px-4 font-weight-bold" onclick="toggleForm()">
                            Cancel
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── Leave History ────────────────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Leave Request History</h6>
        <span class="badge bg-light border text-dark">{{ $history->count() }} record(s)</span>
    </div>
    <div class="card-body p-4">

        {{-- Filters --}}
        <form method="GET" action="{{ route('employee.leave.index') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Status</label>
                <select name="status" class="form-select shadow-sm">
                    <option value="">All Status</option>
                    @foreach (['pending', 'approved', 'rejected'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Leave Type</label>
                <select name="type" class="form-select shadow-sm">
                    <option value="">All Types</option>
                    @foreach ($leaveTypes as $lt)
                        <option value="{{ $lt->id }}" {{ request('type') == $lt->id ? 'selected' : '' }}>
                            {{ $lt->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-secondary flex-grow-1 font-weight-bold shadow-sm">Filter</button>
                <a href="{{ route('employee.leave.index') }}" class="btn btn-outline-dark font-weight-bold">Clear</a>
            </div>
        </form>

        {{-- Table --}}
        @if ($history->isEmpty())
            <div class="text-center text-muted py-5 bg-light rounded border">
                <span class="font-weight-bold d-block">No leave requests found.</span>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="border-0 font-weight-bold ps-3 py-3">Type</th>
                            <th class="border-0 font-weight-bold py-3">Start</th>
                            <th class="border-0 font-weight-bold py-3">End</th>
                            <th class="border-0 font-weight-bold py-3">Days</th>
                            <th class="border-0 font-weight-bold py-3">Reason</th>
                            <th class="border-0 font-weight-bold py-3">Status</th>
                            <th class="border-0 font-weight-bold py-3">Submitted</th>
                            <th class="border-0 font-weight-bold py-3">Reviewed By</th>
                            <th class="border-0 font-weight-bold py-3 text-center pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $lr)
                            <tr class="border-bottom">
                                <td class="font-weight-bold text-dark ps-3 text-nowrap">
                                    {{ $lr->leaveType?->name ?? '—' }}
                                </td>
                                <td class="text-nowrap font-weight-bold text-secondary">{{ $lr->start_date->format('M d, Y') }}</td>
                                <td class="text-nowrap font-weight-bold text-secondary">{{ $lr->end_date->format('M d, Y') }}</td>
                                <td class="font-weight-bold">{{ $lr->days }} <span class="text-muted small fw-normal">days</span></td>
                                <td class="text-muted small" style="max-width:180px;white-space:normal">
                                    {{ $lr->reason }}
                                    @if ($lr->isRejected() && $lr->rejection_reason)
                                        <div class="text-dark font-weight-bold mt-1 border-top pt-1">
                                            Reason: {{ $lr->rejection_reason }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    @switch($lr->status)
                                        @case('pending')
                                            <span class="badge bg-light border text-dark px-2 py-1">Pending</span>
                                            @break
                                        @case('approved')
                                            <span class="badge bg-secondary px-2 py-1">Approved</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-light border text-muted px-2 py-1">Rejected</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="text-nowrap text-muted small font-weight-bold">
                                    {{ $lr->created_at->format('M d, Y') }}
                                </td>
                                <td class="text-nowrap text-dark small font-weight-bold">
                                    {{ $lr->reviewer?->fullName ?? '—' }}
                                </td>
                                <td class="text-center pe-3">
                                    @if ($lr->isPending())
                                        <form method="POST"
                                              action="{{ route('employee.leave.destroy', $lr->id) }}"
                                              id="withdraw-form-{{ $lr->id }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-dark font-weight-bold" onclick="confirmWithdraw({{ $lr->id }})">
                                            Cancel
                                        </button>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
/* ─────────────────────────────────────────────────────────────
   SERVER DATA
   ───────────────────────────────────────────────────────────── */
const DAY_OFFS   = @json($restDaysArray ?? [0, 6]);
const FILED_DATES = @json($filedDates ?? []);

/* ─────────────────────────────────────────────────────────────
   STATE
   ───────────────────────────────────────────────────────────── */
let calMonth    = new Date(); calMonth.setDate(1);
let startDate   = null;
let endDate     = null;
let formVisible = {{ $errors->any() || old('leave_type_id') ? 'true' : 'false' }};

/* ─────────────────────────────────────────────────────────────
   HELPERS
   ───────────────────────────────────────────────────────────── */
const pad     = n => String(n).padStart(2, '0');
const toYMD   = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
const fmtDate = s => {
    const [y,m,d] = s.split('-');
    return new Date(s+'T00:00:00').toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'});
};

const isOff    = ds => DAY_OFFS.includes(new Date(ds+'T00:00:00').getDay());
const isFiled  = ds => FILED_DATES.includes(ds);
const isPast   = ds => ds < toYMD(new Date());

function countWorkingDays(s, e) {
    let n = 0;
    const cur = new Date(s+'T00:00:00');
    const end = new Date(e+'T00:00:00');
    while (cur <= end) {
        if (!isOff(toYMD(cur))) n++;
        cur.setDate(cur.getDate()+1);
    }
    return n;
}

/* ─────────────────────────────────────────────────────────────
   CALENDAR
   ───────────────────────────────────────────────────────────── */
function renderCalendar() {
    const year     = calMonth.getFullYear();
    const month    = calMonth.getMonth();
    const firstDow = new Date(year, month, 1).getDay();
    const days     = new Date(year, month+1, 0).getDate();
    const prevLast = new Date(year, month, 0).getDate();

    document.getElementById('calMonthLabel').textContent =
        calMonth.toLocaleDateString('en-PH',{month:'long',year:'numeric'});

    let cells = [];
    for (let i = firstDow-1; i >= 0; i--)
        cells.push({day: prevLast-i, cur: false, ds: null});
    for (let d = 1; d <= days; d++)
        cells.push({day: d, cur: true, ds:`${year}-${pad(month+1)}-${pad(d)}`});
    while (cells.length < 42)
        cells.push({day: cells.length-firstDow-days+1, cur: false, ds: null});

    let html = '';
    for (let row = 0; row < 6; row++) {
        html += '<div class="row g-2 mb-2 align-items-stretch">';
        for (let col = 0; col < 7; col++) {
            const cell = cells[row*7+col];
            
            if (!cell.cur || !cell.ds) {
                html += `<div class="col px-1"><div class="border border-light rounded p-2 text-center bg-light opacity-50 d-flex flex-column h-100" style="min-height:75px;">
                    <span class="small font-weight-bold text-muted text-end w-100">${cell.day}</span>
                </div></div>`;
                continue;
            }

            const ds         = cell.ds;
            const off        = isOff(ds);
            const filed      = isFiled(ds);
            const past       = isPast(ds);
            const isStart    = ds === startDate;
            const isEnd      = ds === endDate;
            const inRange    = startDate && endDate && ds > startDate && ds < endDate;
            const selectable = !off && !filed && !past;

            let cellClass = 'border rounded p-2 d-flex flex-column h-100 transition-all ';
            let style = 'min-height: 75px; ';
            let numCls = 'small font-weight-bold text-end w-100 ';
            let click = '';
            let badge = '';

            if (isStart || isEnd) {
                cellClass += 'bg-secondary border-secondary shadow-sm';
                numCls += 'text-white';
                style += 'cursor: pointer;';
            } else if (inRange) {
                cellClass += 'bg-light border-dark';
                numCls += 'text-dark';
                style += 'cursor: pointer;';
            } else if (filed) {
                cellClass += 'bg-light border-light text-muted';
                style += 'cursor: not-allowed; opacity: 0.6;';
                badge = `<span class="badge bg-white border text-muted w-100 mt-auto pt-1" style="font-size: 0.65rem;">Filed</span>`;
            } else if (off || past) {
                cellClass += 'bg-light border-light text-muted';
                style += 'cursor: not-allowed; opacity: 0.5;';
            } else {
                cellClass += 'bg-white border-light';
                numCls += 'text-dark';
                style += 'cursor: pointer;';
            }

            if (selectable) {
                click = `onclick="selectDay('${ds}')"`;
            }

            html += `
                <div class="col px-1">
                    <div class="${cellClass}" style="${style}" ${click}>
                        <span class="${numCls}">${cell.day}</span>
                        ${badge}
                    </div>
                </div>`;
        }
        html += '</div>';
    }

    document.getElementById('calGrid').innerHTML = html;
}

function prevMonth() {
    calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth()-1, 1);
    renderCalendar();
}
function nextMonth() {
    calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth()+1, 1);
    renderCalendar();
}

/* ─────────────────────────────────────────────────────────────
   DATE RANGE SELECTION
   ───────────────────────────────────────────────────────────── */
function selectDay(ds) {
    if (!startDate || (startDate && endDate)) {
        startDate = ds;
        endDate   = null;
    } else {
        if (ds < startDate) {
            endDate   = startDate;
            startDate = ds;
        } else {
            endDate = ds;
        }
    }

    document.getElementById('inputStartDate').value = startDate;
    document.getElementById('inputEndDate').value   = endDate ?? startDate;

    renderCalendar();
    updateRangeInfo();
    updateSummaryBox();
    updateSubmitState();
}

function updateRangeInfo() {
    const box = document.getElementById('selectedRangeInfo');
    if (!startDate) { box.innerHTML = ''; return; }

    const e    = endDate ?? startDate;
    const days = countWorkingDays(startDate, e);

    box.innerHTML = `
        <div class="border rounded p-3 bg-white shadow-sm">
            <div class="d-flex align-items-center flex-wrap mb-2 font-weight-bold text-dark text-uppercase">
                <span>${fmtDate(startDate)}</span>
                ${endDate && endDate !== startDate
                    ? `<span class="text-muted mx-2">→</span><span>${fmtDate(endDate)}</span>`
                    : ''}
            </div>
            <div class="small text-muted font-weight-bold text-uppercase">
                <span class="text-dark font-weight-bold">${days}</span> working day${days !== 1 ? 's' : ''} selected
            </div>
        </div>`;
}

/* ─────────────────────────────────────────────────────────────
   BALANCE INFO + SUMMARY BOX
   ───────────────────────────────────────────────────────────── */
function onTypeChange() {
    updateBalanceInfo();
    updateSummaryBox();
    updateSubmitState();
}

function updateBalanceInfo() {
    const sel = document.getElementById('leaveTypeSelect');
    const box = document.getElementById('balanceInfoBox');

    if (!sel.value) {
        box.className = 'border rounded p-3 bg-light text-center d-flex flex-column justify-content-center';
        box.innerHTML = '<span class="text-muted small font-weight-bold">Select a leave type to see your balance</span>';
        return;
    }

    const opt     = sel.options[sel.selectedIndex];
    const balance = parseFloat(opt.dataset.balance ?? 0);
    const used    = parseFloat(opt.dataset.used    ?? 0);
    const total   = parseFloat(opt.dataset.total   ?? 0);
    const name    = opt.dataset.name ?? '';
    const desc    = opt.dataset.desc ?? '';
    const pct     = total > 0 ? Math.round((balance / total) * 100) : 0;

    box.className = 'border border-secondary rounded p-3 bg-white shadow-sm';
    box.innerHTML = `
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="flex-grow-1">
                <div class="font-weight-bold text-dark text-uppercase">${name}</div>
                ${desc ? `<div class="text-muted small mt-1 font-weight-bold text-uppercase" style="font-size: 0.7rem;">${desc}</div>` : ''}
                <div class="mt-3">
                    <div class="progress mb-2" style="height: 6px; border-radius: 3px;">
                        <div class="progress-bar bg-secondary" style="width: ${pct}%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;">
                        <span>Used: ${used}</span>
                        <span>Total: ${total}</span>
                    </div>
                </div>
            </div>
            <div class="text-end border-start ps-3">
                <div class="h3 font-weight-bold text-dark mb-0 lh-1">${balance}</div>
                <div class="text-muted small font-weight-bold text-uppercase mt-1" style="font-size: 0.65rem;">days left</div>
            </div>
        </div>`;
}

function updateSummaryBox() {
    const box = document.getElementById('leaveSummaryBox');
    if (!startDate) { box.classList.add('d-none'); return; }

    const e    = endDate ?? startDate;
    const days = countWorkingDays(startDate, e);

    const sel  = document.getElementById('leaveTypeSelect');
    const opt  = sel.options[sel.selectedIndex];
    const bal  = sel.value ? parseFloat(opt.dataset.balance ?? 0) : null;
    const rem  = bal !== null ? (bal - days) : null;

    document.getElementById('summaryStart').textContent     = fmtDate(startDate);
    document.getElementById('summaryEnd').textContent       = fmtDate(e);
    document.getElementById('summaryDays').textContent      = `${days} day${days !== 1 ? 's' : ''}`;
    document.getElementById('summaryRemaining').textContent = rem !== null
        ? `${rem} day${rem !== 1 ? 's' : ''}`
        : '—';

    box.classList.remove('d-none');
}

function updateSubmitState() {
    const sel      = document.getElementById('leaveTypeSelect');
    const btn      = document.getElementById('submitLeaveBtn');
    const hasType  = !!sel.value;
    const hasDate  = !!startDate;

    if (!hasType || !hasDate) { btn.disabled = true; return; }

    const e    = endDate ?? startDate;
    const days = countWorkingDays(startDate, e);
    const opt  = sel.options[sel.selectedIndex];
    const bal  = parseFloat(opt.dataset.balance ?? 0);

    btn.disabled = days < 1 || days > bal;
}

/* ─────────────────────────────────────────────────────────────
   FORM TOGGLE
   ───────────────────────────────────────────────────────────── */
function toggleForm() {
    formVisible = !formVisible;
    const card = document.getElementById('leaveFormCard');
    const btn  = document.getElementById('toggleFormBtn');

    if (formVisible) {
        card.classList.remove('d-none');
        btn.textContent = 'Cancel Request';
        btn.classList.replace('btn-secondary', 'btn-outline-dark');
        card.scrollIntoView({behavior:'smooth', block:'start'});
    } else {
        card.classList.add('d-none');
        btn.textContent = 'Apply for Leave';
        btn.classList.replace('btn-outline-dark', 'btn-secondary');
    }
}

if (formVisible) {
    document.getElementById('toggleFormBtn').textContent = 'Cancel Request';
    document.getElementById('toggleFormBtn').classList.replace('btn-secondary', 'btn-outline-dark');
}

/* ─────────────────────────────────────────────────────────────
   WITHDRAW CONFIRM
   ───────────────────────────────────────────────────────────── */
function confirmWithdraw(id) {
    Swal.fire({
        title: 'Cancel this request?',
        text: 'This will permanently remove the leave application.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Cancel It',
        cancelButtonText: 'Keep it',
        confirmButtonColor: '#1a1a1a', // Match Secondary
        cancelButtonColor: '#6c757d',
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('withdraw-form-' + id).submit();
        }
    });
}

/* ─────────────────────────────────────────────────────────────
   INIT
   ───────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    renderCalendar();
    updateBalanceInfo();
});
</script>
@endpush