@extends('layouts.main')

@section('title', 'Overtime Management')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">Overtime Management</li>
    </ol>
@endsection

@section('content')

{{-- Flash messages --}}
@if (session('success'))
    <div class="alert alert-light border border-secondary d-flex justify-content-between align-items-center mb-4" role="alert">
        <span class="font-weight-bold text-dark">{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-light border border-dark d-flex justify-content-between align-items-center mb-4" role="alert">
        <span class="font-weight-bold text-muted">{{ $errors->first() }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Page header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Overtime Management</h4>
        <small class="text-muted font-weight-bold text-uppercase">File overtime requests and track your OT hours</small>
    </div>
    <button class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm" id="toggleFormBtn" onclick="toggleForm()">
        File OT Request
    </button>
</div>

{{-- Summary stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Approved OT Hours</span>
            <span class="h3 font-weight-bold text-dark mb-1">{{ number_format($stats['approved_hours'], 1) }} <span class="h6 text-muted">hrs</span></span>
            <span class="text-muted" style="font-size: 0.7rem;">Approved & paid requests</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">OT Earnings</span>
            <span class="h3 font-weight-bold text-dark mb-1">₱{{ number_format($stats['approved_earnings'], 2) }}</span>
            <span class="text-muted" style="font-size: 0.7rem;">Estimated approved earnings</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Pending Requests</span>
            <span class="h3 font-weight-bold text-dark mb-1">{{ $stats['pending_count'] }}</span>
            <span class="text-muted" style="font-size: 0.7rem;">Awaiting approval</span>
        </div>
    </div>
</div>

{{-- OT Request Form --}}
<div class="card shadow-sm border-0 mb-5 d-none" id="otFormCard">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">New Overtime Request</h6>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('employee.overtime.store') }}" id="otForm">
            @csrf
            <input type="hidden" name="date" id="inputDate">
            <input type="hidden" name="ot_type" id="inputOtType">
            <input type="hidden" name="rate_multiplier" id="inputRateMultiplier">
            <input type="hidden" name="estimated_pay" id="inputEstimatedPay">

            <div class="row g-4">

                {{-- Left: Calendar --}}
                <div class="col-lg-6">
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Select OT Date</label>

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

                    {{-- Selected date info --}}
                    <div class="mt-3" id="selectedDateInfo"></div>

                    {{-- Legend --}}
                    <div class="d-flex flex-wrap align-items-center pt-3 mt-4 border-top text-muted small font-weight-bold text-uppercase gap-3">
                        <div class="d-flex align-items-center">
                            <span class="p-1 border border-dark bg-light rounded-circle me-2"></span> Has Auto-OT
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="p-1 border border-light bg-light rounded-circle me-2"></span> Filed / Past
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-dark me-1">H</span> Holiday
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-secondary me-1">R</span> Rest Day
                        </div>
                    </div>
                </div>

                {{-- Right: OT Type + Reason --}}
                <div class="col-lg-6">
                    <div class="mb-4">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">OT Type</label>
                        <div class="border rounded p-3 bg-light text-center d-flex flex-column justify-content-center" style="min-height: 85px;" id="otTypeDisplay">
                            <span class="text-muted small font-weight-bold">Select a date to auto-detect OT type</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Requested Hours <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg shadow-sm">
                            <input type="number" name="hours" id="inputHours" 
                                class="form-control border-secondary font-weight-bold" step="0.5" min="0.5" max="24" 
                                placeholder="0.0" oninput="updateEstimatedPay()" required disabled>
                            <span class="input-group-text bg-light text-muted font-weight-bold">hrs</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control border shadow-sm p-3" name="reason" id="otReason" rows="3"
                            placeholder="Brief reason for overtime..." required></textarea>
                    </div>

                    <div class="mb-4 d-none" id="estimatedPayBox">
                        <div class="border border-secondary rounded p-3 bg-white shadow-sm text-center">
                            <p class="text-muted small font-weight-bold text-uppercase mb-1">Estimated OT Pay</p>
                            <p class="h3 font-weight-bold text-dark mb-1" id="estimatedPayAmt">₱0.00</p>
                            <p class="mb-0 text-muted small font-weight-bold" id="estimatedPayFormula"></p>
                        </div>
                    </div>

                    @if ($config && ! $config->enforce_limit)
                        <div class="alert alert-light border text-dark small font-weight-bold mb-4 text-center">
                            OT limits are in warning mode — you may still file but HR will review flagged requests.
                        </div>
                    @endif

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-secondary btn-lg flex-grow-1 font-weight-bold shadow-sm" id="submitOTBtn" disabled>
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

{{-- History Filters + Table --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Overtime Request History</h6>
        <span class="badge bg-light border text-dark">{{ $requests->total() }} record(s)</span>
    </div>
    <div class="card-body p-4">

        {{-- Filters --}}
        <form method="GET" action="{{ route('employee.overtime.index') }}" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Date From</label>
                <input type="date" name="from" class="form-control shadow-sm" value="{{ $filters['from'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Date To</label>
                <input type="date" name="to" class="form-control shadow-sm" value="{{ $filters['to'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Status</label>
                <select name="status" class="form-select shadow-sm">
                    <option value="all"     @selected(($filters['status'] ?? 'all') === 'all')>All Status</option>
                    <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pending</option>
                    <option value="approved"@selected(($filters['status'] ?? '') === 'approved')>Approved</option>
                    <option value="paid"    @selected(($filters['status'] ?? '') === 'paid')>Paid</option>
                    <option value="rejected"@selected(($filters['status'] ?? '') === 'rejected')>Rejected</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-secondary flex-grow-1 font-weight-bold shadow-sm">Filter</button>
                <a href="{{ route('employee.overtime.index') }}" class="btn btn-outline-dark font-weight-bold">Clear</a>
            </div>
        </form>

        {{-- Table --}}
        @if ($requests->isEmpty())
            <div class="text-center text-muted py-5 bg-light rounded border">
                <span class="font-weight-bold d-block">No overtime requests found.</span>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="border-0 font-weight-bold ps-3 py-3">Date</th>
                            <th class="border-0 font-weight-bold py-3">Hours</th>
                            <th class="border-0 font-weight-bold py-3">OT Type</th>
                            <th class="border-0 font-weight-bold py-3">Rate</th>
                            <th class="border-0 font-weight-bold py-3">Est. Pay</th>
                            <th class="border-0 font-weight-bold py-3">Reason</th>
                            <th class="border-0 font-weight-bold py-3">Status</th>
                            <th class="border-0 font-weight-bold py-3">Submitted</th>
                            <th class="border-0 font-weight-bold py-3">Reviewed By</th>
                            <th class="border-0 font-weight-bold py-3 text-center pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $req)
                            <tr class="border-bottom">
                                <td class="text-nowrap font-weight-bold text-dark ps-3">
                                    {{ $req->date->format('M d, Y') }}
                                </td>
                                <td class="font-weight-bold text-secondary">{{ $req->hours }} <span class="text-muted small fw-normal">hrs</span></td>
                                <td><span class="small font-weight-bold">{{ $req->ot_type }}</span></td>
                                <td class="text-nowrap font-weight-bold">{{ $req->rate_multiplier }}×</td>
                                <td class="text-nowrap font-weight-bold text-dark">
                                    ₱{{ number_format($req->estimated_pay, 2) }}
                                </td>
                                <td class="text-muted small" style="max-width:180px; white-space:normal;">
                                    {{ $req->reason ?: '—' }}
                                </td>
                                <td class="text-nowrap">
                                    @switch($req->status)
                                        @case('pending')
                                            <span class="badge bg-light border text-dark px-2 py-1">Pending</span>
                                            @break
                                        @case('approved')
                                            <span class="badge bg-secondary px-2 py-1">Approved</span>
                                            @break
                                        @case('paid')
                                            <span class="badge border border-secondary text-dark px-2 py-1">Paid</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-light border text-muted px-2 py-1">Rejected</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="text-nowrap text-muted small font-weight-bold">
                                    {{ $req->created_at->format('M d, Y') }}
                                </td>
                                <td class="text-nowrap text-dark small font-weight-bold">
                                    {{ $req->reviewer?->fullName ?? '—' }}
                                </td>
                                <td class="text-center pe-3">
                                    @if (in_array($req->status, ['pending', 'rejected']))
                                        <form method="POST"
                                            action="{{ route('employee.overtime.destroy', $req) }}"
                                            onsubmit="return confirm('Cancel this overtime request?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-dark font-weight-bold">
                                                Cancel
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $requests->links() }}
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
const HOURLY_RATE       = {{ (float) $user->hourlyRate }};
const ATTENDANCE        = @json($attendanceRecords);   
const HOLIDAYS          = @json($holidays);            
const OT_RATES          = @json($overtimeRates);       
const FILED_DATES       = @json($filedDates->keys());  
const DAY_OFF_MAP       = @json($restDaysArray ?? [0, 6]); 

/* ─────────────────────────────────────────────────────────────
   STATE
   ───────────────────────────────────────────────────────────── */
let calMonth     = new Date();
calMonth.setDate(1);
let selectedDate = null;
let detectedType = null;
let formVisible  = false;

/* ─────────────────────────────────────────────────────────────
   HELPERS
   ───────────────────────────────────────────────────────────── */
const pad     = n => String(n).padStart(2, '0');
const toYMD   = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
const fmtDate = s => {
    const d = new Date(s + 'T00:00:00');
    return d.toLocaleDateString('en-PH', { month:'short', day:'numeric', year:'numeric' });
};
const fmtPHP  = n => '₱' + parseFloat(n).toLocaleString('en-PH', {
    minimumFractionDigits: 2, maximumFractionDigits: 2
});

const getHoliday   = ds => HOLIDAYS[ds] || null;
const getAtt       = ds => ATTENDANCE[ds] || null;
const isFiled      = ds => FILED_DATES.includes(ds);
const isRestDay    = ds => DAY_OFF_MAP.includes(new Date(ds + 'T00:00:00').getDay());
const getRate      = name => OT_RATES[name] || 1.25;

function autoDetectType(ds) {
    const hol   = getHoliday(ds);
    const rest  = isRestDay(ds);
    const att   = getAtt(ds);
    const night = att && String(att.shift_type || '').toLowerCase() === 'night';

    let base = 'Regular Overtime';

    if (hol && rest) {
        base = hol.type === 'regular'
            ? 'Regular Holiday Overtime on Rest Day'
            : 'Special Holiday Overtime on Rest Day';
    } else if (hol) {
        base = hol.type === 'regular'
            ? 'Regular Holiday Overtime'
            : 'Special Holiday Overtime';
    } else if (rest) {
        base = 'Rest Day Overtime';
    }

    const noNight = [
        'Regular Holiday Overtime', 'Special Holiday Overtime',
        'Regular Holiday Overtime on Rest Day', 'Special Holiday Overtime on Rest Day',
    ];
    if (night && !noNight.includes(base)) base += ' + Night Shift';

    return base;
}

function typeDescription(name) {
    const descriptions = {
        'Regular Overtime':                      'Regular OT rate (1.25×)',
        'Regular Overtime + Night Shift':        'OT (1.25) × Night Shift (1.10) = 1.375×',
        'Rest Day Overtime':                     'Rest Day (1.30) × OT (1.30) = 1.69×',
        'Rest Day Overtime + Night Shift':       'Rest Day OT (1.69) × Night (1.10) = 1.859×',
        'Special Holiday Overtime':              'Special Holiday (1.30) × OT (1.30) = 1.69×',
        'Special Holiday Overtime on Rest Day':  'Special+Rest (1.50) × OT (1.30) = 1.95×',
        'Regular Holiday Overtime':              'Regular Holiday (2.00) × OT (1.30) = 2.60×',
        'Regular Holiday Overtime on Rest Day':  'Regular+Rest (2.60) × OT (1.30) = 3.38×',
    };
    return descriptions[name] || '';
}

/* ─────────────────────────────────────────────────────────────
   CALENDAR
   ───────────────────────────────────────────────────────────── */
function renderCalendar() {
    const year  = calMonth.getFullYear();
    const month = calMonth.getMonth();

    document.getElementById('calMonthLabel').textContent =
        calMonth.toLocaleDateString('en-PH', { month:'long', year:'numeric' });

    const firstDow  = new Date(year, month, 1).getDay();
    const daysInMon = new Date(year, month + 1, 0).getDate();
    const prevDays  = new Date(year, month, 0).getDate();

    let cells = [];
    for (let i = firstDow - 1; i >= 0; i--)
        cells.push({ day: prevDays - i, current: false, ds: null });
    for (let d = 1; d <= daysInMon; d++)
        cells.push({ day: d, current: true, ds: `${year}-${pad(month+1)}-${pad(d)}` });
    while (cells.length < 42)
        cells.push({ day: cells.length - firstDow - daysInMon + 1, current: false, ds: null });

    let html = '';
    for (let row = 0; row < 6; row++) {
        html += '<div class="row g-2 mb-2 align-items-stretch">';
        for (let col = 0; col < 7; col++) {
            const cell = cells[row * 7 + col];
            const ds   = cell.ds;

            if (!cell.current || !ds) {
                html += `<div class="col"><div class="border border-light rounded p-2 text-center bg-light opacity-50 d-flex flex-column h-100" style="min-height:75px;">
                    <span class="small font-weight-bold text-muted text-end w-100">${cell.day}</span>
                </div></div>`;
                continue;
            }

            const att      = getAtt(ds);
            const hasOT    = att && att.overtime_hours > 0;
            const filed    = isFiled(ds);
            const hol      = getHoliday(ds);
            const rest     = isRestDay(ds);
            const isSelect = ds === selectedDate;
            const isPast   = ds < toYMD(new Date());

            let cellClass = 'border rounded p-2 d-flex flex-column h-100 ';
            let style = 'min-height: 75px; ';
            let click = '';
            let badge = '';

            // Monochromatic Styling Logic
            if (filed) {
                cellClass += 'bg-light border-light text-muted';
                style += 'cursor: not-allowed; opacity: 0.6;';
                badge = `<span class="badge bg-white border text-muted w-100 mt-auto pt-1" style="font-size: 0.65rem;">Filed</span>`;
            } else if (isPast && !att) {
                cellClass += 'bg-light border-light text-muted';
                style += 'cursor: not-allowed; opacity: 0.5;';
            } else {
                style += 'cursor: pointer; transition: all 0.2s; ';
                click = `onclick="selectDate('${ds}')"`;
                
                if (isSelect) {
                    cellClass += 'bg-white border-secondary shadow-sm';
                } else if (hasOT) {
                    cellClass += 'bg-light border-dark';
                    badge = `<span class="badge bg-white border border-dark text-dark w-100 mt-auto pt-1" style="font-size: 0.65rem;">Auto-OT</span>`;
                } else {
                    cellClass += 'bg-white border-light';
                }
            }

            let indicators = '';
            if (hol)  indicators += `<span class="fw-bold text-dark me-1" style="font-size: 0.7rem;">H</span>`;
            if (rest) indicators += `<span class="fw-bold text-secondary" style="font-size: 0.7rem;">R</span>`;

            html += `
                <div class="col px-1">
                    <div class="${cellClass}" style="${style}" ${click}>
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span>${indicators}</span>
                            <span class="small font-weight-bold ${isSelect ? 'text-dark' : 'text-muted'}">${cell.day}</span>
                        </div>
                        ${badge}
                    </div>
                </div>`;
        }
        html += '</div>';
    }

    document.getElementById('calGrid').innerHTML = html;
}

function prevMonth() {
    calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth() - 1, 1);
    renderCalendar();
}
function nextMonth() {
    calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth() + 1, 1);
    renderCalendar();
}

/* ─────────────────────────────────────────────────────────────
   DATE SELECTION
   ───────────────────────────────────────────────────────────── */
function selectDate(ds) {
    if (isFiled(ds)) return;

    selectedDate = ds;
    detectedType = autoDetectType(ds);

    document.getElementById('inputDate').value           = ds;
    document.getElementById('inputOtType').value         = detectedType;
    document.getElementById('inputRateMultiplier').value = getRate(detectedType);
    
    const hoursInput = document.getElementById('inputHours');
    hoursInput.disabled = false;
    
    const att = getAtt(ds);
    if (att && att.overtime_hours > 0) {
        hoursInput.value = att.overtime_hours;
    } else {
        hoursInput.value = ''; 
    }

    renderCalendar();
    updateOTTypeDisplay();
    updateSelectedDateInfo();
    updateEstimatedPay();
}

function updateSelectedDateInfo() {
    const box = document.getElementById('selectedDateInfo');
    if (!selectedDate) { box.innerHTML = ''; return; }

    const att  = getAtt(selectedDate);
    const hol  = getHoliday(selectedDate);
    const otH  = att ? att.overtime_hours : 0;

    const holBadge = hol
        ? `<span class="badge bg-white border border-secondary text-dark ms-2">${hol.name}</span>`
        : '';

    let textInfo = '';
    if (att && att.is_ongoing) {
        textInfo = `<span class="text-secondary font-weight-bold">Shift currently ongoing</span>`;
    } else if (att) {
        textInfo = `Recorded: <strong>${att.hours_worked} total hrs</strong> <span class="text-muted mx-1">|</span> <span class="font-weight-bold text-dark">${otH} hrs auto-detected OT</span>`;
    } else {
        textInfo = `<span class="text-muted font-weight-bold">Advance filing (No attendance record yet)</span>`;
    }

    box.innerHTML = `
        <div class="border rounded p-3 bg-white shadow-sm">
            <div class="d-flex align-items-center flex-wrap mb-2">
                <span class="font-weight-bold text-dark">${fmtDate(selectedDate)}</span>
                ${holBadge}
            </div>
            <div class="small text-uppercase font-weight-bold">
                ${textInfo}
            </div>
        </div>`;
}

function updateOTTypeDisplay() {
    const box = document.getElementById('otTypeDisplay');
    if (!detectedType) {
        box.innerHTML = '<span class="text-muted small font-weight-bold">Select a date to auto-detect OT type</span>';
        box.className = 'border rounded p-3 bg-light text-center d-flex flex-column justify-content-center';
        return;
    }

    const multiplier = getRate(detectedType);
    const desc       = typeDescription(detectedType);
    const hol        = getHoliday(selectedDate);
    const rest       = isRestDay(selectedDate);
    const att        = getAtt(selectedDate);
    const night      = att && String(att.shift_type || '').toLowerCase() === 'night';

    let tags = '';
    if (hol)   tags += `<span class="badge bg-white border border-dark text-dark me-1">${hol.type === 'regular' ? 'Regular Holiday' : 'Special Holiday'}</span>`;
    if (rest)  tags += `<span class="badge bg-secondary me-1">Rest Day</span>`;
    if (night) tags += `<span class="badge bg-dark me-1">Night Shift</span>`;

    box.className = 'border border-secondary rounded p-3 bg-white shadow-sm text-start';
    box.innerHTML = `
        <div class="d-flex align-items-start justify-content-between gap-2">
            <div>
                <div class="font-weight-bold text-dark">${detectedType}</div>
                <div class="text-muted small mt-1 font-weight-bold text-uppercase">${desc}</div>
                ${tags ? `<div class="mt-2">${tags}</div>` : ''}
            </div>
            <span class="badge bg-secondary font-weight-bold" style="font-size: 1rem;">${multiplier}×</span>
        </div>`;
}

function updateEstimatedPay() {
    const box      = document.getElementById('estimatedPayBox');
    const amtEl    = document.getElementById('estimatedPayAmt');
    const frmEl    = document.getElementById('estimatedPayFormula');
    const hoursVal = document.getElementById('inputHours').value;
    const btn      = document.getElementById('submitOTBtn');

    if (!selectedDate || !detectedType || !hoursVal || hoursVal <= 0) { 
        box.classList.add('d-none'); 
        btn.disabled = true;
        return; 
    }

    const otH        = parseFloat(hoursVal);
    const multiplier = getRate(detectedType);
    const pay        = otH * HOURLY_RATE * multiplier;

    document.getElementById('inputEstimatedPay').value = pay.toFixed(2);

    amtEl.textContent = fmtPHP(pay);
    frmEl.textContent = `${otH} hrs × ${fmtPHP(HOURLY_RATE)}/hr × ${multiplier}×`;
    box.classList.remove('d-none');
    
    btn.disabled = false;
}

/* ─────────────────────────────────────────────────────────────
   FORM TOGGLE
   ───────────────────────────────────────────────────────────── */
function toggleForm() {
    formVisible = !formVisible;
    const card = document.getElementById('otFormCard');
    const btn  = document.getElementById('toggleFormBtn');

    if (formVisible) {
        card.classList.remove('d-none');
        btn.textContent = 'Cancel Request';
        btn.classList.replace('btn-secondary', 'btn-outline-dark');
    } else {
        card.classList.add('d-none');
        btn.textContent = 'File OT Request';
        btn.classList.replace('btn-outline-dark', 'btn-secondary');
        resetForm();
    }
}

function resetForm() {
    selectedDate = null;
    detectedType = null;
    document.getElementById('otReason').value                    = '';
    document.getElementById('inputDate').value                   = '';
    document.getElementById('inputHours').value                  = '';
    document.getElementById('inputHours').disabled               = true;
    document.getElementById('submitOTBtn').disabled              = true;
    document.getElementById('estimatedPayBox').classList.add('d-none');
    document.getElementById('selectedDateInfo').innerHTML        = '';
    
    const box = document.getElementById('otTypeDisplay');
    box.className = 'border rounded p-3 bg-light text-center d-flex flex-column justify-content-center';
    box.innerHTML = '<span class="text-muted small font-weight-bold">Select a date to auto-detect OT type</span>';
    
    renderCalendar();
}

/* ─────────────────────────────────────────────────────────────
   INIT
   ───────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    renderCalendar();
});
</script>
@endpush