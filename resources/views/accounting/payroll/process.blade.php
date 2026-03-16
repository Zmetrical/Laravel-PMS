@extends('layouts.main')

@section('title', 'Process Payroll – ' . $period->label)

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.payroll.periods.index') }}" class="text-secondary text-decoration-none">Payroll Periods</a></li>
        <li class="breadcrumb-item active text-muted">Process – {{ $period->label }}</li>
    </ol>
@endsection

@push('styles')
<style>
    /* Custom active state for the employee list to match the secondary (dark) theme */
    .employee-item { cursor: pointer; transition: background-color 0.2s; }
    .employee-item:hover { background-color: #f8f9fa; }
    .employee-item.active { background-color: #1a1a1a !important; color: #fff !important; border-color: #1a1a1a !important; }
    .employee-item.active .text-muted { color: #adb5bd !important; }
    .employee-item.active .text-dark { color: #fff !important; }
    .employee-item.active .badge.bg-light { background-color: #343a40 !important; border-color: #495057 !important; color: #fff !important; }
</style>
@endpush

@section('content')

@include('components.alerts')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Process Payroll — {{ $period->label }}</h4>
        <small class="text-muted font-weight-bold text-uppercase">
            Pay Date: <span class="text-dark">{{ $period->pay_date->format('M d, Y') }}</span>
            <span class="mx-2">|</span>
            <span id="progress-label" class="text-secondary">{{ $savedCount }} of {{ $totalEmployees }} Processed</span>
        </small>
    </div>

    {{-- Process Actions (Only on this page) --}}
    @if($period->isProcessing())
    <div class="d-flex gap-2">
        <button class="btn btn-outline-dark font-weight-bold px-4 shadow-sm" id="btn-save-all">Save All</button>
        <button class="btn btn-secondary font-weight-bold px-4 shadow-sm" id="btn-release-all">Release Payroll</button>
    </div>
    @endif
</div>

{{-- YOUR TAB DESIGN --}}
<div class="mb-4">
    <div class="d-inline-flex gap-1 bg-white border p-1 rounded shadow-sm">
        {{-- Process Tab (Only accessible when Processing) --}}
        @if ($period->isProcessing())
            <a href="{{ route('accounting.payroll.periods.process', $period) }}" 
               class="btn btn-sm font-weight-bold px-4 {{ request()->routeIs('accounting.payroll.periods.process') ? 'btn-secondary shadow-sm text-white' : 'btn-light border-0 text-muted' }}">
                Process
            </a>
        @else
            <span class="btn btn-sm font-weight-bold px-4 btn-light border-0 text-muted" 
                  style="opacity: 0.4; cursor: not-allowed;" title="Only available while processing">
                Process
            </span>
        @endif

        {{-- Records Tab (Accessible unless in Draft) --}}
        @if (!$period->isDraft())
            <a href="{{ route('accounting.payroll.periods.records', $period) }}" 
               class="btn btn-sm font-weight-bold px-4 {{ request()->routeIs('accounting.payroll.periods.records') ? 'btn-secondary shadow-sm text-white' : 'btn-light border-0 text-muted' }}">
                Records
            </a>
        @else
            <span class="btn btn-sm font-weight-bold px-4 btn-light border-0 text-muted" 
                  style="opacity: 0.4; cursor: not-allowed;" title="Not available in draft status">
                Records
            </span>
        @endif

        {{-- Summary Tab (Only accessible when Released or Closed) --}}
        @if ($period->isReleased() || $period->isClosed())
            <a href="{{ route('accounting.payroll.periods.summary', $period) }}" 
               class="btn btn-sm font-weight-bold px-4 {{ request()->routeIs('accounting.payroll.periods.summary') ? 'btn-secondary shadow-sm text-white' : 'btn-light border-0 text-muted' }}">
                Summary
            </a>
        @else
            <span class="btn btn-sm font-weight-bold px-4 btn-light border-0 text-muted" 
                  style="opacity: 0.4; cursor: not-allowed;" title="Available only after payroll is released">
                Summary
            </span>
        @endif
    </div>
</div>

{{-- Progress bar --}}
<div class="progress bg-light border shadow-sm mb-4" style="height: 8px; border-radius: 4px;">
    <div class="progress-bar bg-secondary"
         id="progress-bar"
         role="progressbar"
         style="width: {{ $totalEmployees > 0 ? round(($savedCount / $totalEmployees) * 100) : 0 }}%">
    </div>
</div>

<div class="row g-4 mb-5">

    {{-- ===== LEFT: EMPLOYEE LIST ===== --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100 d-flex flex-column">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-shrink-0">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Employees</h6>
                <span class="badge bg-light border text-dark px-2 py-1" id="emp-count">{{ $totalEmployees }}</span>
            </div>
            
            <div class="bg-light border-bottom p-4 flex-shrink-0">
                <div class="input-group shadow-sm mb-3">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="emp-search" class="form-control border-start-0 font-weight-bold text-dark ps-0" placeholder="Search name or position…">
                </div>
                <select id="dept-filter" class="form-select shadow-sm font-weight-bold text-dark">
                    <option value="all">All Departments</option>
                    @foreach ($employees->pluck('department')->filter()->unique()->sort() as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <div class="overflow-auto bg-white flex-grow-1" style="max-height:650px;" id="employee-list">
                @foreach ($employees as $emp)
                    @php $recordStatus = $processedIds[$emp->id] ?? null; @endphp
                    <div class="border-bottom px-4 py-3 d-flex align-items-center gap-3 employee-item"
                         data-id="{{ $emp->id }}"
                         data-name="{{ strtolower($emp->fullName) }}"
                         data-position="{{ strtolower($emp->position) }}"
                         data-dept="{{ $emp->department }}"
                         role="button">
                        
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="font-weight-bold text-dark text-truncate d-block">{{ $emp->fullName }}</span>
                                <span class="record-badge flex-shrink-0 ms-2" data-default="{{ $recordStatus }}">
                                    @if ($recordStatus === 'released')
                                        <span class="badge bg-secondary px-2 py-1 text-uppercase" style="font-size: 0.65rem;">Released</span>
                                    @elseif ($recordStatus === 'draft')
                                        <span class="badge bg-light border text-dark px-2 py-1 text-uppercase" style="font-size: 0.65rem;">Saved</span>
                                    @endif
                                </span>
                            </div>
                            <div class="text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;">
                                {{ $emp->position }} <span class="mx-1">|</span> {{ $emp->department }}
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== RIGHT: DETAIL PANEL ===== --}}
    <div class="col-lg-8" id="panel-detail" style="display:none">

        {{-- Employee Info --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="font-weight-bold text-dark mb-1" id="detail-name"></h5>
                    <div class="text-muted small font-weight-bold text-uppercase" id="detail-meta"></div>
                </div>
                <div class="text-end">
                    <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.7rem;">Monthly Salary</div>
                    <div class="h4 font-weight-bold text-dark mb-0" id="detail-salary"></div>
                </div>
            </div>
            <div class="card-footer bg-light p-3 border-top">
                <div class="row g-3 text-center">
                    <div class="col-4 border-end">
                        <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Employee ID</div>
                        <span class="font-weight-bold text-dark" id="detail-id"></span>
                    </div>
                    <div class="col-4 border-end">
                        <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Daily Rate</div>
                        <span class="font-weight-bold text-dark" id="detail-daily-rate"></span>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Hourly Rate</div>
                        <span class="font-weight-bold text-dark" id="detail-hourly-rate"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- DTR Card --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Daily Time Record</h6>
                <span class="badge bg-light border text-dark px-2 py-1" id="dtr-count">0 records</span>
            </div>

            <div id="dtr-loading" class="text-center py-5 d-none">
                <div class="spinner-border spinner-border-sm text-secondary me-2" role="status"></div>
                <span class="text-muted font-weight-bold text-uppercase small">Loading records…</span>
            </div>

            <div id="dtr-summary" class="bg-light border-bottom p-3 d-none">
                <div class="row g-3 text-center">
                    <div class="col-3 border-end">
                        <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Work Days</div>
                        <span class="font-weight-bold text-dark" id="stat-workdays">—</span>
                    </div>
                    <div class="col-3 border-end">
                        <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Absent</div>
                        <span class="font-weight-bold text-dark" id="stat-absent">—</span>
                    </div>
                    <div class="col-3 border-end">
                        <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Late (min)</div>
                        <span class="font-weight-bold text-dark" id="stat-late">—</span>
                    </div>
                    <div class="col-3">
                        <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Undertime (min)</div>
                        <span class="font-weight-bold text-dark" id="stat-ut">—</span>
                    </div>
                </div>
            </div>

            <div class="overflow-auto bg-white" style="max-height:400px" id="dtr-records">
                <div class="text-center text-muted py-5 font-weight-bold text-uppercase" id="dtr-placeholder">
                    Select an employee to view records
                </div>
            </div>
        </div>

        {{-- Payroll Computation Card --}}
        <div class="card shadow-sm border-0 mb-5" id="card-payroll" style="display:none">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Payroll Computation</h6>
                <div class="d-flex gap-3 align-items-center">
                    <span class="badge bg-light border text-muted px-2 py-1 text-uppercase" id="record-status-badge">Not Saved</span>
                    <button class="btn btn-sm btn-secondary font-weight-bold px-4 shadow-sm" id="btn-save-record">
                        Save Record
                    </button>
                </div>
            </div>
            <div class="card-body p-4">

                {{-- Deferred balance notice --}}
                <div class="alert alert-light border border-secondary shadow-sm d-none mb-4" id="deferred-notice">
                    <span class="font-weight-bold text-dark text-uppercase small d-block mb-1">Deferred Balance Notice</span>
                    <span class="small text-muted font-weight-bold">
                        Deferred balance of <strong class="text-dark" id="deferred-amount"></strong> from the previous
                        period is included in deductions.
                    </span>
                </div>

                <div class="row g-4 mb-4">
                    {{-- Earnings --}}
                    <div class="col-md-6">
                        <div class="border rounded bg-white shadow-sm h-100 overflow-hidden">
                            <div class="bg-light px-3 py-2 border-bottom text-muted small font-weight-bold text-uppercase">Earnings</div>
                            <table class="table table-hover align-middle mb-0">
                                <tbody id="earnings-body"></tbody>
                                <tfoot>
                                    <tr class="bg-light border-top">
                                        <td class="font-weight-bold text-dark ps-3 py-3">Gross Pay</td>
                                        <td class="font-weight-bold text-dark text-end pe-3 py-3" id="summary-gross"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- Deductions --}}
                    <div class="col-md-6">
                        <div class="border rounded bg-white shadow-sm h-100 overflow-hidden">
                            <div class="bg-light px-3 py-2 border-bottom text-muted small font-weight-bold text-uppercase">Deductions</div>
                            <table class="table table-hover align-middle mb-0">
                                <tbody id="deductions-body"></tbody>
                                <tfoot>
                                    <tr class="bg-light border-top">
                                        <td class="font-weight-bold text-dark ps-3 py-3">Total Deductions</td>
                                        <td class="font-weight-bold text-dark text-end pe-3 py-3" id="summary-deductions"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Loan Deductions --}}
                <div id="loans-section" class="border rounded bg-white shadow-sm mb-4 overflow-hidden d-none">
                    <div class="bg-light px-3 py-2 border-bottom text-muted small font-weight-bold text-uppercase">Loan Deductions</div>
                    <table class="table table-hover align-middle mb-0">
                        <tbody id="loans-body"></tbody>
                        <tfoot>
                            <tr class="bg-light border-top">
                                <td class="font-weight-bold text-dark ps-3 py-3">Loans Subtotal</td>
                                <td class="font-weight-bold text-dark text-end pe-3 py-3" id="summary-loans"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Net Pay --}}
                <div class="border border-secondary rounded bg-white p-4 shadow-sm text-center">
                    <h6 class="text-dark font-weight-bold text-uppercase mb-1">Net Pay</h6>
                    <h2 class="font-weight-bold text-dark mb-0 display-6" id="summary-net"></h2>
                </div>

            </div>
        </div>

    </div>

    {{-- Empty state --}}
    <div class="col-lg-8" id="panel-empty">
        <div class="text-center bg-white border rounded shadow-sm p-5 w-100 h-100 d-flex flex-column align-items-center justify-content-center">
            <i class="bi bi-person-fill text-muted mb-3 d-block" style="font-size: 3rem;"></i>
            <span class="font-weight-bold text-dark d-block mb-1">No Employee Selected</span>
            <small class="text-muted font-weight-bold text-uppercase">Select an employee from the list to compute payroll.</small>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {

    // ── Config ───────────────────────────────────────────────────────────────
    const BASE_URL = '{{ url("/accounting/payroll/periods/{$period->id}/process") }}';
    const CSRF     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    let SAVED      = {{ $savedCount }};
    const TOTAL    = {{ $totalEmployees }};

    // ── State ────────────────────────────────────────────────────────────────
    let activeEmployeeId = null;

    // ── Helpers ──────────────────────────────────────────────────────────────
    const peso = n => '₱' + Number(n || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    function tableRow(label, value, sub = null) {
        return `
        <tr class="border-bottom">
            <td class="text-muted small font-weight-bold text-uppercase py-3 ps-3 border-0">
                ${esc(label)}
                ${sub ? `<div style="font-size:.65rem" class="text-muted fw-normal mt-1">${esc(sub)}</div>` : ''}
            </td>
            <td class="text-end font-weight-bold text-dark py-3 pe-3 border-0">${peso(value)}</td>
        </tr>`;
    }

    function esc(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str ?? ''));
        return d.innerHTML;
    }

    function updateProgress(delta = 0) {
        SAVED = Math.max(0, Math.min(TOTAL, SAVED + delta));
        const pct = TOTAL > 0 ? Math.round((SAVED / TOTAL) * 100) : 0;
        document.getElementById('progress-bar').style.width = pct + '%';
        document.getElementById('progress-label').textContent = `${SAVED} of ${TOTAL} Processed`;
    }

    // ── Employee filter ──────────────────────────────────────────────────────
    function filterList() {
        const q    = document.getElementById('emp-search').value.toLowerCase();
        const dept = document.getElementById('dept-filter').value;
        let visible = 0;

        document.querySelectorAll('.employee-item').forEach(el => {
            const match = (!q || el.dataset.name.includes(q) || el.dataset.position.includes(q))
                       && (dept === 'all' || el.dataset.dept === dept);
            el.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        document.getElementById('emp-count').textContent = visible;
    }

    // ── Select employee ──────────────────────────────────────────────────────
    function selectEmployee(id) {
        if (activeEmployeeId === id) return;
        activeEmployeeId = id;

        document.querySelectorAll('.employee-item').forEach(el =>
            el.classList.toggle('active', el.dataset.id === id)
        );

        document.getElementById('panel-detail').style.display = '';
        document.getElementById('panel-empty').style.display  = 'none';
        document.getElementById('dtr-loading').classList.remove('d-none');
        document.getElementById('dtr-records').innerHTML = '';
        document.getElementById('dtr-summary').classList.add('d-none');
        document.getElementById('card-payroll').style.display = 'none';
        document.getElementById('dtr-count').textContent = '0 records';

        fetch(`${BASE_URL}/${id}/data`, {
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('dtr-loading').classList.add('d-none');
            renderEmployeeInfo(data.employee);
            renderDTR(data.attendance, data.meta);
            renderPayroll(data.computed, data.meta, data.existing);
        })
        .catch(() => {
            document.getElementById('dtr-loading').classList.add('d-none');
            document.getElementById('dtr-records').innerHTML =
                `<div class="text-center text-muted font-weight-bold py-5 text-uppercase">Failed to load data.</div>`;
        });
    }

    // ── Employee info ─────────────────────────────────────────────────────────
    function renderEmployeeInfo(emp) {
        document.getElementById('detail-name').textContent      = emp.fullName;
        document.getElementById('detail-meta').innerHTML        = `${emp.position} <span class="mx-1">|</span> ${emp.department} <span class="mx-1">|</span> ${emp.employmentStatus}`;
        document.getElementById('detail-salary').textContent    = peso(emp.basicSalary);
        document.getElementById('detail-id').textContent        = emp.id;
        document.getElementById('detail-daily-rate').textContent  = peso(emp.dailyRate);
        document.getElementById('detail-hourly-rate').textContent = peso(emp.hourlyRate);
    }

    // ── DTR ───────────────────────────────────────────────────────────────────
    function renderDTR(records, meta) {
        document.getElementById('dtr-count').textContent =
            `${records.length} record${records.length !== 1 ? 's' : ''}`;

        document.getElementById('stat-workdays').textContent = meta.work_days ?? '—';
        document.getElementById('stat-absent').textContent   = meta.absent_days ?? '—';
        document.getElementById('stat-late').textContent     = meta.late_minutes ?? '—';
        document.getElementById('stat-ut').textContent       = meta.ut_minutes ?? '—';
        document.getElementById('dtr-summary').classList.remove('d-none');

        if (!records.length) {
            document.getElementById('dtr-records').innerHTML =
                `<div class="text-center text-muted font-weight-bold text-uppercase py-5">No attendance records for this period</div>`;
            return;
        }

        const STATUS_BADGE = {
            present    : '',
            absent     : '<span class="badge bg-light border border-secondary text-muted px-2 py-1 text-uppercase" style="font-size:0.6rem;">Absent</span>',
            late       : '<span class="badge bg-light border text-dark px-2 py-1 text-uppercase" style="font-size:0.6rem;">Late</span>',
            half_day   : '<span class="badge bg-light border text-dark px-2 py-1 text-uppercase" style="font-size:0.6rem;">Half Day</span>',
            leave      : '<span class="badge bg-white border border-dark text-dark px-2 py-1 text-uppercase" style="font-size:0.6rem;">Leave</span>',
            holiday    : '<span class="badge bg-secondary text-white px-2 py-1 text-uppercase" style="font-size:0.6rem;">Holiday</span>',
            incomplete : '<span class="badge bg-light border text-muted px-2 py-1 text-uppercase" style="font-size:0.6rem;">Incomplete</span>',
            rest_day   : '<span class="badge bg-light border text-muted px-2 py-1 text-uppercase" style="font-size:0.6rem;">Rest Day</span>',
        };

        document.getElementById('dtr-records').innerHTML = records.map(r => {
            const lateBadge = r.late_minutes > 0
                ? `<span class="badge bg-light border text-dark ms-1 px-2 py-1 text-uppercase" style="font-size:0.6rem;">${r.late_minutes}m late</span>` : '';
            const utBadge   = r.undertime_minutes > 0
                ? `<span class="badge bg-light border text-dark ms-1 px-2 py-1 text-uppercase" style="font-size:0.6rem;">${r.undertime_minutes}m UT</span>` : '';
            const bioBadge  = r.is_biometric
                ? `<span class="badge bg-light border text-muted ms-1 px-2 py-1 text-uppercase" style="font-size:0.6rem;">Bio</span>` : '';

            return `
            <div class="px-4 py-3 border-bottom d-flex align-items-start gap-4 bg-white ${r.status === 'absent' ? 'opacity-75' : ''}">
                <div style="min-width:90px">
                    <div class="font-weight-bold text-dark">${r.date}</div>
                    <div class="text-muted font-weight-bold text-uppercase" style="font-size:.65rem">${r.day_name}</div>
                </div>
                <div class="flex-grow-1">
                    <div class="mb-2">
                        ${STATUS_BADGE[r.status] ?? ''}${lateBadge}${utBadge}${bioBadge}
                    </div>
                    <div class="d-flex gap-4 text-uppercase font-weight-bold" style="font-size:.7rem">
                        <span class="text-muted">In: <span class="text-dark">${r.time_in ?? '—'}</span></span>
                        <span class="text-muted">Out: <span class="text-dark">${r.time_out ?? '—'}</span></span>
                        <span class="text-muted">Hrs: <span class="text-dark">${r.hours_worked.toFixed(2)}</span></span>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    // ── Payroll Computation ───────────────────────────────────────────────────
    function renderPayroll(computed, meta, existing) {

        // Deferred balance notice
        const deferredNotice = document.getElementById('deferred-notice');
        if (meta.deferred_from_prev > 0) {
            document.getElementById('deferred-amount').textContent = peso(meta.deferred_from_prev);
            deferredNotice.classList.remove('d-none');
        } else {
            deferredNotice.classList.add('d-none');
        }

        // Earnings
        const earnings = [
            { label: 'Basic Pay',          value: computed.basic_pay,    sub: null },
            { label: 'Overtime Pay',       value: computed.overtime_pay,
              sub: meta.ot_hours > 0 ? `${meta.ot_hours}h approved` : null },
            { label: 'Night Differential', value: computed.night_diff_pay,
              sub: meta.nd_hours > 0 ? `${meta.nd_hours}h × 10%` : null },
            { label: 'Holiday Pay',        value: computed.holiday_pay,  sub: null },
            { label: 'Leave Pay',          value: computed.leave_pay,
              sub: meta.leave_days > 0 ? `${meta.leave_days} day(s)` : null },
            { label: 'Allowances',         value: computed.allowances,   sub: null },
        ].filter(e => e.value > 0);

        document.getElementById('earnings-body').innerHTML =
            earnings.map(e => tableRow(e.label, e.value, e.sub)).join('') ||
            `<tr><td colspan="2" class="text-center text-muted font-weight-bold text-uppercase py-4 border-0">No earnings</td></tr>`;

        // Deductions
        const deductions = [
            { label: 'SSS',             value: computed.sss },
            { label: 'PhilHealth',      value: computed.philhealth },
            { label: 'Pag-IBIG',        value: computed.pagibig },
            { label: 'Withholding Tax', value: computed.withholding_tax },
            { label: 'Late',            value: computed.late_deductions,
              sub: meta.late_minutes > 0 ? `${meta.late_minutes} min` : null },
            { label: 'Undertime',       value: computed.undertime_deductions,
              sub: meta.ut_minutes > 0 ? `${meta.ut_minutes} min` : null },
            { label: 'Absent',          value: computed.absent_deductions,
              sub: meta.absent_days > 0 ? `${meta.absent_days} day(s)` : null },
            { label: 'Other',           value: computed.other_deductions },
            { label: 'Deferred (prev period)', value: meta.deferred_from_prev },
        ].filter(d => d.value > 0);

        document.getElementById('deductions-body').innerHTML =
            deductions.map(d => tableRow(d.label, d.value, d.sub ?? null)).join('') ||
            `<tr><td colspan="2" class="text-center text-muted font-weight-bold text-uppercase py-4 border-0">No deductions</td></tr>`;

        // Loan deductions
        const loansSection = document.getElementById('loans-section');
        const loanRows     = meta.loan_deductions ?? [];
        const loanTotal    = loanRows.reduce((s, l) => s + l.amount, 0);

        if (loanRows.length > 0) {
            document.getElementById('loans-body').innerHTML = loanRows.map(l =>
                tableRow(l.label, l.amount, `Balance after: ${peso(l.balance_after)}`)
            ).join('');
            document.getElementById('summary-loans').textContent = peso(loanTotal);
            loansSection.classList.remove('d-none');
        } else {
            loansSection.classList.add('d-none');
        }

        // Totals
        document.getElementById('summary-gross').textContent      = peso(computed.gross_pay);
        document.getElementById('summary-deductions').textContent = peso(computed.total_deductions);
        document.getElementById('summary-net').textContent        = peso(computed.net_pay);

        // Record status badge
        const badge = document.getElementById('record-status-badge');
        if (existing) {
            badge.textContent = existing.status === 'released' ? 'Released' : 'Saved';
            badge.className   = `badge px-2 py-1 text-uppercase ${existing.status === 'released' ? 'bg-secondary text-white' : 'bg-light border text-dark'}`;
        } else {
            badge.textContent = 'Not Saved';
            badge.className   = 'badge bg-light border text-muted px-2 py-1 text-uppercase';
        }

        // Disable save for released records
        const saveBtn = document.getElementById('btn-save-record');
        if(saveBtn) saveBtn.disabled = existing?.status === 'released';

        document.getElementById('card-payroll').style.display = '';
    }

    // ── Save single record ────────────────────────────────────────────────────
    function saveRecord() {
        if (!activeEmployeeId) return;

        const btn = document.getElementById('btn-save-record');
        if (!btn) return;

        btn.disabled    = true;
        btn.innerHTML   = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

        fetch(`${BASE_URL}/${activeEmployeeId}/save`, {
            method : 'POST',
            headers: {
                Accept          : 'application/json',
                'Content-Type'  : 'application/json',
                'X-CSRF-TOKEN'  : CSRF,
            },
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled    = false;
            btn.textContent = 'Save Record';

            if (!data.success) {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#1a1a1a' });
                return;
            }

            const badge = document.getElementById('record-status-badge');
            badge.textContent = 'Saved';
            badge.className   = 'badge bg-light border text-dark px-2 py-1 text-uppercase';

            const empItem = document.querySelector(`.employee-item[data-id="${activeEmployeeId}"]`);
            if (empItem) {
                const badgeSpan = empItem.querySelector('.record-badge');
                if (badgeSpan) {
                    badgeSpan.innerHTML = '<span class="badge bg-light border text-dark px-2 py-1 text-uppercase" style="font-size: 0.65rem;">Saved</span>';
                }
            }

            updateProgress(1);

            Swal.fire({
                icon: 'success', title: 'Saved',
                text: data.message,
                timer: 2000, showConfirmButton: false,
                toast: true, position: 'top-end',
            });
        })
        .catch(() => {
            btn.disabled    = false;
            btn.textContent = 'Save Record';
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save. Please try again.', confirmButtonColor: '#1a1a1a' });
        });
    }

    // ── Save All ──────────────────────────────────────────────────────────────
    function saveAll() {
        Swal.fire({
            title             : 'Compute & Save All?',
            text              : 'This will compute and save payroll for all active employees. Already-released records will be skipped.',
            icon              : 'question',
            showCancelButton  : true,
            confirmButtonText : 'Save All',
            confirmButtonColor: '#1a1a1a',
            cancelButtonColor : '#6c757d',
        }).then(result => {
            if (!result.isConfirmed) return;

            const btn = document.getElementById('btn-save-all');
            if(!btn) return;
            
            btn.disabled    = true;
            btn.innerHTML   = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

            fetch(`{{ url("/accounting/payroll/periods/{$period->id}/process/save-all") }}`, {
                method : 'POST',
                headers: {
                    Accept         : 'application/json',
                    'Content-Type' : 'application/json',
                    'X-CSRF-TOKEN' : CSRF,
                },
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled    = false;
                btn.textContent = 'Save All';

                if (data.success) {
                    Swal.fire({
                        icon              : 'success',
                        title             : 'Done',
                        text              : data.message,
                        confirmButtonColor: '#1a1a1a',
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#1a1a1a' });
                }
            })
            .catch(() => {
                btn.disabled    = false;
                btn.textContent = 'Save All';
                Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed.', confirmButtonColor: '#1a1a1a' });
            });
        });
    }

    // ── Release All ───────────────────────────────────────────────────────────
    function releaseAll() {
        Swal.fire({
            title             : 'Release Payroll?',
            text              : 'All saved (draft) records will be released and employees will be able to view their payslips. This cannot be undone.',
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonText : 'Yes, Release',
            confirmButtonColor: '#1a1a1a',
            cancelButtonColor : '#6c757d',
        }).then(result => {
            if (!result.isConfirmed) return;

            const btn = document.getElementById('btn-release-all');
            if(!btn) return;

            btn.disabled    = true;
            btn.innerHTML   = '<span class="spinner-border spinner-border-sm me-2"></span>Releasing…';

            fetch(`{{ url("/accounting/payroll/periods/{$period->id}/process/release-all") }}`, {
                method : 'POST',
                headers: {
                    Accept         : 'application/json',
                    'Content-Type' : 'application/json',
                    'X-CSRF-TOKEN' : CSRF,
                },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon              : 'success',
                        title             : 'Payroll Released',
                        text              : data.message,
                        confirmButtonColor: '#1a1a1a',
                    }).then(() => {
                        if (data.redirect) window.location.href = data.redirect;
                        else window.location.reload();
                    });
                } else {
                    btn.disabled    = false;
                    btn.textContent = 'Release Payroll';
                    Swal.fire({ icon: 'error', title: 'Cannot Release', text: data.message, confirmButtonColor: '#1a1a1a' });
                }
            })
            .catch(() => {
                btn.disabled    = false;
                btn.textContent = 'Release Payroll';
                Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed.', confirmButtonColor: '#1a1a1a' });
            });
        });
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    document.getElementById('emp-search')?.addEventListener('input', filterList);
    document.getElementById('dept-filter')?.addEventListener('change', filterList);
    document.getElementById('btn-save-record')?.addEventListener('click', saveRecord);
    document.getElementById('btn-save-all')?.addEventListener('click', saveAll);
    document.getElementById('btn-release-all')?.addEventListener('click', releaseAll);

    document.querySelectorAll('.employee-item').forEach(el =>
        el.addEventListener('click', () => selectEmployee(el.dataset.id))
    );

})();
</script>
@endpush