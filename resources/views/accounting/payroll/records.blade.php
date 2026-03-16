@extends('layouts.main')

@section('title', 'Payroll Records — ' . $period->label)

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.payroll.periods.index') }}" class="text-secondary text-decoration-none">Payroll Periods</a></li>
        <li class="breadcrumb-item active text-muted">Records — {{ $period->label }}</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')
{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Payroll Records</h4>
        <small class="text-muted font-weight-bold text-uppercase">
            {{ $period->label }} <span class="mx-2">|</span> Pay Date: <span class="text-dark">{{ $period->pay_date->format('M d, Y') }}</span>
        </small>
    </div>
</div>

{{-- YOUR TAB DESIGN --}}
<div class="mb-5">
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
{{-- Totals Strip --}}
<div class="row g-3 mb-5">
    <div class="col-md-3">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Employees</span>
            <span class="h3 font-weight-bold text-dark mb-0">{{ $totals['count'] }}</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Total Gross Pay</span>
            <span class="h3 font-weight-bold text-dark mb-0">₱{{ number_format($totals['gross_pay'], 2) }}</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Total Deductions</span>
            <span class="h3 font-weight-bold text-dark mb-0">₱{{ number_format($totals['total_deductions'], 2) }}</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border border-secondary rounded bg-light p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-dark small font-weight-bold text-uppercase mb-2">Total Net Pay</span>
            <span class="h2 font-weight-bold text-dark mb-0">₱{{ number_format($totals['net_pay'], 2) }}</span>
        </div>
    </div>
</div>

{{-- Records Table --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">
            Records <span class="badge bg-light border text-dark ms-2 px-2 py-1">{{ $totals['count'] }}</span>
        </h6>
        <div class="d-flex gap-3 align-items-center">
            <div class="input-group shadow-sm" style="width: 250px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="record-search" class="form-control border-start-0 font-weight-bold text-dark ps-0" placeholder="Search employee…">
            </div>
            <select id="dept-filter" class="form-select shadow-sm font-weight-bold text-dark" style="width: 200px;">
                <option value="">All Departments</option>
                @foreach ($records->pluck('employee.department')->filter()->unique()->sort() as $dept)
                    <option value="{{ $dept }}">{{ $dept }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 ps-4 py-3">Employee</th>
                        <th class="border-0 py-3">Department</th>
                        <th class="border-0 py-3 text-end">Basic Pay</th>
                        <th class="border-0 py-3 text-end">Gross Pay</th>
                        <th class="border-0 py-3 text-end">Deductions</th>
                        <th class="border-0 py-3 text-end">Net Pay</th>
                        <th class="border-0 py-3 text-center">Status</th>
                        <th class="border-0 py-3 pe-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                    <tr class="border-bottom bg-white record-row"
                        data-name="{{ strtolower($record->employee?->fullName) }}"
                        data-dept="{{ $record->employee?->department }}">
                        <td class="ps-4 py-3">
                            <div class="font-weight-bold text-dark mb-1">
                                {{ $record->employee?->fullName ?? 'Unknown' }}
                            </div>
                            <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 0.7rem;">
                                {{ $record->employee?->position }}
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="text-muted small font-weight-bold text-uppercase" style="font-size: 0.7rem;">{{ $record->employee?->department ?? '—' }}</span>
                        </td>
                        <td class="text-end font-weight-bold text-secondary py-3">
                            ₱{{ number_format($record->basic_pay, 2) }}
                        </td>
                        <td class="text-end font-weight-bold text-secondary py-3">
                            ₱{{ number_format($record->gross_pay, 2) }}
                        </td>
                        <td class="text-end font-weight-bold text-danger py-3">
                            ₱{{ number_format($record->total_deductions, 2) }}
                        </td>
                        <td class="text-end font-weight-bold text-dark py-3">
                            ₱{{ number_format($record->net_pay, 2) }}
                        </td>
                        <td class="text-center py-3">
                            @if ($record->status === 'released')
                                <span class="badge bg-secondary px-3 py-2 text-uppercase">Released</span>
                            @else
                                <span class="badge bg-light border text-muted px-3 py-2 text-uppercase">Draft</span>
                            @endif
                        </td>
                        <td class="text-center pe-4 py-3">
                            <button class="btn btn-sm btn-light border text-dark font-weight-bold px-3 shadow-sm"
                                    title="View Breakdown"
                                    onclick="openBreakdown({{ $record->id }})">
                                View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 bg-light text-muted">
                            <span class="font-weight-bold text-dark d-block mb-1">No records found.</span>
                            <small class="text-muted font-weight-bold text-uppercase">Go to the process page to compute payroll.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Breakdown Modal --}}
<div class="modal fade" id="breakdownModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase">Payroll Breakdown</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 bg-light" id="breakdown-body">
                <div class="text-center py-5">
                    <div class="spinner-border spinner-border-sm text-secondary"></div>
                </div>
            </div>
            
            <div class="modal-footer bg-white py-3">
                <button type="button" class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const RECORDS = @json($recordsJson);

// ── Helpers ───────────────────────────────────────────────────────────────────
const peso = n => '₱' + Number(n || 0).toLocaleString('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

function trow(label, value) {
    return `<tr class="border-bottom">
        <td class="text-muted small font-weight-bold text-uppercase py-2 ps-3 border-0">${label}</td>
        <td class="text-end font-weight-bold text-secondary py-2 pe-3 border-0">${peso(value)}</td>
    </tr>`;
}

// ── Filter ────────────────────────────────────────────────────────────────────
document.getElementById('record-search').addEventListener('input', filterRows);
document.getElementById('dept-filter').addEventListener('change', filterRows);

function filterRows() {
    const q    = document.getElementById('record-search').value.toLowerCase();
    const dept = document.getElementById('dept-filter').value.toLowerCase();

    document.querySelectorAll('.record-row').forEach(row => {
        const matchName = !q    || row.dataset.name.includes(q);
        const matchDept = !dept || row.dataset.dept.toLowerCase() === dept;
        row.style.display = matchName && matchDept ? '' : 'none';
    });
}

// ── Breakdown modal ───────────────────────────────────────────────────────────
function openBreakdown(id) {
    const r = RECORDS.find(x => x.id === id);
    if (!r) return;

    const earnings = [
        ['Basic Pay',            r.basic_pay],
        ['Overtime Pay',         r.overtime_pay],
        ['Night Differential',   r.night_diff_pay],
        ['Holiday Pay',          r.holiday_pay],
        ['Rest Day Pay',         r.rest_day_pay],
        ['Leave Pay',            r.leave_pay],
        ['Additional Shift Pay', r.additional_shift_pay],
        ['Allowances',           r.allowances],
    ].filter(([, v]) => v > 0);

    const deductions = [
        ['SSS',             r.sss],
        ['PhilHealth',      r.philhealth],
        ['Pag-IBIG',        r.pagibig],
        ['Withholding Tax', r.withholding_tax],
        ['Late',            r.late_deductions],
        ['Undertime',       r.undertime_deductions],
        ['Absent',          r.absent_deductions],
        ['Other',           r.other_deductions],
        ['Deferred (prev)', r.deferred_balance],
    ].filter(([, v]) => v > 0);

    const statusBadge = r.status === 'released'
        ? '<span class="badge bg-secondary px-3 py-2 text-uppercase" style="letter-spacing: 1px;">Released</span>'
        : '<span class="badge bg-light border text-muted px-3 py-2 text-uppercase">Draft</span>';

    document.getElementById('breakdown-body').innerHTML = `
        <div class="bg-white border rounded p-4 mb-4 shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="font-weight-bold text-dark mb-1">${r.employee_name}</h5>
                <div class="text-muted small font-weight-bold text-uppercase">
                    ${r.employee_position} <span class="mx-1">|</span> ${r.employee_department}
                </div>
            </div>
            <div class="text-end">
                <div class="mb-1">${statusBadge}</div>
                ${r.released_at ? `<div class="text-muted small font-weight-bold text-uppercase" style="font-size: 0.65rem;">Released: ${r.released_at}</div>` : ''}
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="text-muted small font-weight-bold text-uppercase mb-0">Earnings</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <tbody>${earnings.map(([l, v]) => trow(l, v)).join('')}</tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td class="font-weight-bold text-dark ps-3 py-3">Gross Pay</td>
                                    <td class="font-weight-bold text-dark text-end pe-3 py-3">${peso(r.gross_pay)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="text-muted small font-weight-bold text-uppercase mb-0">Deductions</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <tbody>${deductions.map(([l, v]) => trow(l, v)).join('')}</tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td class="font-weight-bold text-dark ps-3 py-3">Total Deductions</td>
                                    <td class="font-weight-bold text-dark text-end pe-3 py-3">${peso(r.total_deductions)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="border border-secondary rounded bg-white p-4 shadow-sm text-center">
            <h6 class="text-dark font-weight-bold text-uppercase mb-1">Net Pay</h6>
            <h2 class="font-weight-bold text-dark mb-0 display-6">${peso(r.net_pay)}</h2>
            ${r.notes ? `
                <div class="mt-3 pt-3 border-top">
                    <small class="text-muted font-weight-bold text-uppercase">Note: <span class="text-dark">${r.notes}</span></small>
                </div>
            ` : ''}
        </div>
    `;

    bootstrap.Modal.getOrCreateInstance(document.getElementById('breakdownModal')).show();
}
</script>
@endpush