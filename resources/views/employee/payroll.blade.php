@extends('layouts.main')

@section('title', 'Payroll & Payslips')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">Payroll &amp; Payslips</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Payroll &amp; Payslips</h4>
        <small class="text-muted font-weight-bold text-uppercase">View your salary details and government contributions</small>
    </div>
</div>

{{-- Latest Payslip Card --}}
<div id="latest-card" class="card shadow-sm border-0 mb-5 d-none">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Latest Payslip</h6>
        <div class="d-flex align-items-center">
            <span id="latest-period-label" class="text-muted small font-weight-bold text-uppercase me-3"></span>
            <span class="badge bg-secondary px-3 py-2 text-uppercase" style="letter-spacing: 1px;">Released</span>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
                    <span class="text-muted small font-weight-bold text-uppercase mb-2">Gross Pay</span>
                    <span class="h3 font-weight-bold text-dark mb-0" id="latest-gross">₱0.00</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
                    <span class="text-muted small font-weight-bold text-uppercase mb-2">Total Deductions</span>
                    <span class="h3 font-weight-bold text-dark mb-0" id="latest-deductions">₱0.00</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border border-secondary rounded bg-light p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
                    <span class="text-dark small font-weight-bold text-uppercase mb-2">Net Pay</span>
                    <span class="h2 font-weight-bold text-dark mb-0" id="latest-net">₱0.00</span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-3">
            <button class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm" id="btn-view-latest">
                View Breakdown
            </button>
            <button class="btn btn-outline-dark font-weight-bold px-4 py-2" id="btn-download-latest">
                Download PDF
            </button>
        </div>
    </div>
</div>

{{-- Payslip History --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Payslip History</h6>
        <div class="d-flex gap-2">
            <select id="filter-year" class="form-select form-select-sm shadow-sm font-weight-bold text-muted w-auto px-3 py-1"></select>
            <select id="filter-month" class="form-select form-select-sm shadow-sm font-weight-bold text-muted w-auto px-3 py-1">
                <option value="">All Months</option>
                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                    <option value="{{ $m }}">{{ $m }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-body p-4">

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="payslip-table">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 font-weight-bold ps-3 py-3">Period</th>
                        <th class="border-0 font-weight-bold py-3">Type</th>
                        <th class="border-0 font-weight-bold py-3 text-end">Gross Pay</th>
                        <th class="border-0 font-weight-bold py-3 text-end">Deductions</th>
                        <th class="border-0 font-weight-bold py-3 text-end">Net Pay</th>
                        <th class="border-0 font-weight-bold py-3">Pay Date</th>
                        <th class="border-0 font-weight-bold py-3 text-center pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody id="payslip-tbody">
                    {{-- Populated by JS --}}
                </tbody>
            </table>
        </div>

        {{-- Empty State --}}
        <div id="empty-state" class="text-center py-5 bg-light rounded border mt-3 d-none">
            <span class="font-weight-bold text-dark d-block mb-1">No payslips found.</span>
            <small class="text-muted font-weight-bold text-uppercase">Your payslips will appear here once released by Accounting.</small>
        </div>

    </div>
</div>

{{-- ── Breakdown Modal ──────────────────────────────────────────────── --}}
<div class="modal fade" id="breakdownModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded">

            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase" id="breakdownModalLabel">Payslip Breakdown</h6>
                {{-- Bootstrap 5 Native Close --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-light">

                <div class="bg-white border rounded p-4 mb-4 shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-1" id="modal-employee-name">{{ auth()->user()->fullName }}</h5>
                        <div class="text-muted small font-weight-bold text-uppercase">
                            {{ auth()->user()->position }} <span class="mx-1">|</span> {{ auth()->user()->department }}
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="font-weight-bold text-dark mb-1" id="modal-period-label"></div>
                        <div class="text-muted small font-weight-bold text-uppercase">Pay Date: <span id="modal-pay-date"></span></div>
                    </div>
                </div>

                {{-- Deferred Balance Notice --}}
                <div id="modal-deferred-notice" class="alert alert-light border border-secondary d-none mb-4">
                    <h6 class="font-weight-bold text-dark mb-1">Deferred Balance Applied</h6>
                    <p class="mb-0 small text-muted">
                        An unpaid balance of <strong class="text-dark" id="modal-deferred-amount"></strong> from the previous period has been carried over and is included in your total deductions.
                    </p>
                </div>

                <div class="row g-4">
                    {{-- Earnings Column --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="text-muted small font-weight-bold text-uppercase mb-0">Earnings</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-hover mb-0">
                                    <tbody id="modal-earnings-body"></tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td class="font-weight-bold text-dark ps-3 py-3">Gross Pay</td>
                                            <td class="font-weight-bold text-dark text-end pe-3 py-3" id="modal-gross-pay"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Deductions Column --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="text-muted small font-weight-bold text-uppercase mb-0">Deductions</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-hover mb-0">
                                    <tbody id="modal-deductions-body"></tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td class="font-weight-bold text-dark ps-3 py-2">Subtotal</td>
                                            <td class="font-weight-bold text-dark text-end pe-3 py-2" id="modal-deductions-subtotal"></td>
                                        </tr>
                                    </tfoot>
                                </table>

                                {{-- Loans --}}
                                <div id="modal-loans-section">
                                    <div class="px-3 py-2 bg-light border-top border-bottom text-muted small font-weight-bold text-uppercase">Loans</div>
                                    <table class="table table-hover mb-0">
                                        <tbody id="modal-loans-body"></tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <td class="font-weight-bold text-dark ps-3 py-2 border-top">Subtotal</td>
                                                <td class="font-weight-bold text-dark text-end pe-3 py-2 border-top" id="modal-loans-subtotal"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                {{-- Total Deductions Footer --}}
                                <div class="bg-white border-top d-flex justify-content-between align-items-center p-3">
                                    <span class="font-weight-bold text-dark">Total Deductions</span>
                                    <span class="font-weight-bold text-dark" id="modal-total-deductions"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Net Pay Callout --}}
                <div class="border border-secondary rounded bg-white p-4 shadow-sm mt-4 text-center">
                    <h6 class="text-dark font-weight-bold text-uppercase mb-1">Net Pay</h6>
                    <h2 class="font-weight-bold text-dark mb-0 display-6" id="modal-net-pay"></h2>
                    <div id="modal-notes-wrap" class="mt-3 pt-3 border-top d-none">
                        <small class="text-muted font-weight-bold text-uppercase">Note: <span id="modal-notes" class="text-dark"></span></small>
                    </div>
                </div>

            </div>

            <div class="modal-footer bg-white py-3">
                <button type="button" class="btn btn-outline-dark font-weight-bold px-4" id="btn-modal-download">
                    Download PDF
                </button>
                {{-- Bootstrap 5 Native Close --}}
                <button type="button" class="btn btn-secondary font-weight-bold px-4" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Data from server ──────────────────────────────────────────────────────────
const PAYSLIPS = @json($payslips);

// ── State ─────────────────────────────────────────────────────────────────────
let activePayslip = null;

// ── Boot ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    populateYearFilter();
    renderLatestCard();
    renderTable();

    document.getElementById('filter-year').addEventListener('change', renderTable);
    document.getElementById('filter-month').addEventListener('change', renderTable);

    document.getElementById('btn-view-latest')?.addEventListener('click', () => {
        if (PAYSLIPS.length) openModal(PAYSLIPS[0]);
    });

    document.getElementById('btn-download-latest')?.addEventListener('click', () => {
        if (PAYSLIPS.length) downloadPayslip(PAYSLIPS[0]);
    });

    document.getElementById('btn-modal-download')?.addEventListener('click', () => {
        if (activePayslip) downloadPayslip(activePayslip);
    });
});

// ── Year filter ───────────────────────────────────────────────────────────────
function populateYearFilter() {
    const sel   = document.getElementById('filter-year');
    const years = [...new Set(PAYSLIPS.map(p => p.pay_date?.substring(0, 4)).filter(Boolean))].sort().reverse();

    const allOpt = document.createElement('option');
    allOpt.value = '';
    allOpt.textContent = 'All Years';
    sel.appendChild(allOpt);

    years.forEach(y => {
        const opt = document.createElement('option');
        opt.value = y;
        opt.textContent = y;
        sel.appendChild(opt);
    });
}

// ── Latest card ───────────────────────────────────────────────────────────────
function renderLatestCard() {
    if (!PAYSLIPS.length) return;
    const p = PAYSLIPS[0];
    document.getElementById('latest-card').classList.remove('d-none');
    document.getElementById('latest-period-label').textContent = p.period;
    document.getElementById('latest-gross').textContent        = peso(p.gross_pay);
    document.getElementById('latest-deductions').textContent   = peso(p.total_deductions);
    document.getElementById('latest-net').textContent          = peso(p.net_pay);
}

// ── Table ─────────────────────────────────────────────────────────────────────
function renderTable() {
    const year  = document.getElementById('filter-year').value;
    const month = document.getElementById('filter-month').value.toLowerCase();

    const filtered = PAYSLIPS.filter(p => {
        const matchYear  = !year  || (p.pay_date?.startsWith(year));
        const matchMonth = !month || p.period.toLowerCase().includes(month);
        return matchYear && matchMonth;
    });

    const tbody      = document.getElementById('payslip-tbody');
    const emptyState = document.getElementById('empty-state');
    tbody.innerHTML  = '';

    if (!filtered.length) {
        emptyState.classList.remove('d-none');
        return;
    }

    emptyState.classList.add('d-none');

    filtered.forEach(p => {
        const tr = document.createElement('tr');
        tr.className = 'border-bottom';
        tr.innerHTML = `
            <td class="align-middle font-weight-bold text-dark ps-3 py-3">${esc(p.period)}</td>
            <td class="align-middle py-3">
                <span class="badge bg-light border text-dark px-2 py-1">${periodTypeLabel(p.period_type)}</span>
            </td>
            <td class="align-middle text-end font-weight-bold text-secondary py-3">${peso(p.gross_pay)}</td>
            <td class="align-middle text-end font-weight-bold text-secondary py-3">${peso(p.total_deductions)}</td>
            <td class="align-middle text-end font-weight-bold text-dark py-3">${peso(p.net_pay)}</td>
            <td class="align-middle text-muted small font-weight-bold py-3">${formatDate(p.pay_date)}</td>
            <td class="align-middle text-center pe-3 py-3">
                <button class="btn btn-sm btn-outline-dark font-weight-bold me-1" title="View Breakdown"
                    onclick="openModal(PAYSLIPS.find(x => x.id === ${p.id}))">
                    View
                </button>
                <button class="btn btn-sm btn-light border text-dark font-weight-bold" title="Download PDF"
                    onclick="downloadPayslip(PAYSLIPS.find(x => x.id === ${p.id}))">
                    PDF
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// ── Breakdown Modal ───────────────────────────────────────────────────────────
function openModal(payslip) {
    if (!payslip) return;
    activePayslip = payslip;

    document.getElementById('modal-period-label').textContent = payslip.period;
    document.getElementById('modal-pay-date').textContent     = formatDate(payslip.pay_date);

    // Deferred notice
    const deferredNotice = document.getElementById('modal-deferred-notice');
    if (payslip.deferred_balance > 0) {
        document.getElementById('modal-deferred-amount').textContent = peso(payslip.deferred_balance);
        deferredNotice.classList.remove('d-none');
    } else {
        deferredNotice.classList.add('d-none');
    }

    // Earnings
    document.getElementById('modal-earnings-body').innerHTML = buildEarnings(payslip);
    document.getElementById('modal-gross-pay').textContent   = peso(payslip.gross_pay);

    // Deductions
    document.getElementById('modal-deductions-body').innerHTML       = buildMandatoryDeductions(payslip);
    document.getElementById('modal-deductions-subtotal').textContent = peso(calcMandatoryTotal(payslip));

    // Loans
    const { html: loanHtml, total: loanTotal } = buildLoanDeductions(payslip);
    const loansSection = document.getElementById('modal-loans-section');
    if (loanTotal > 0) {
        document.getElementById('modal-loans-body').innerHTML       = loanHtml;
        document.getElementById('modal-loans-subtotal').textContent = peso(loanTotal);
        loansSection.classList.remove('d-none');
    } else {
        loansSection.classList.add('d-none');
    }

    document.getElementById('modal-total-deductions').textContent = peso(payslip.total_deductions);

    // Net pay
    document.getElementById('modal-net-pay').textContent = peso(payslip.net_pay);

    // Notes
    const notesWrap = document.getElementById('modal-notes-wrap');
    if (payslip.notes) {
        document.getElementById('modal-notes').textContent = payslip.notes;
        notesWrap.classList.remove('d-none');
    } else {
        notesWrap.classList.add('d-none');
    }

    // Bootstrap 5 Native Modal Triger
    const modalEl = document.getElementById('breakdownModal');
    const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    bsModal.show();
}

function buildEarnings(p) {
    const rows = [];
    rows.push(row('Basic Pay',             p.basic_pay));
    if (p.overtime_pay > 0)         rows.push(row('Overtime Pay',             p.overtime_pay));
    if (p.night_diff_pay > 0)       rows.push(row('Night Differential',       p.night_diff_pay));
    if (p.holiday_pay > 0)          rows.push(row('Holiday Pay',              p.holiday_pay));
    if (p.rest_day_pay > 0)         rows.push(row('Rest Day Pay',             p.rest_day_pay));
    if (p.leave_pay > 0)            rows.push(row('Leave Pay',                p.leave_pay));
    if (p.additional_shift_pay > 0) rows.push(row('Additional Shift Pay',     p.additional_shift_pay));
    if (p.allowances > 0)           rows.push(row('Allowances',               p.allowances));
    return rows.join('');
}

// ── Mandatory deductions (statutory + attendance) ─────────────────────────────
function buildMandatoryDeductions(p) {
    const rows = [];
    if (p.sss > 0)                  rows.push(row('SSS Contribution',    p.sss));
    if (p.philhealth > 0)           rows.push(row('PhilHealth',          p.philhealth));
    if (p.pagibig > 0)              rows.push(row('Pag-IBIG',            p.pagibig));
    if (p.withholding_tax > 0)      rows.push(row('Withholding Tax',     p.withholding_tax));
    if (p.late_deductions > 0)      rows.push(row('Late',                p.late_deductions));
    if (p.undertime_deductions > 0) rows.push(row('Undertime',           p.undertime_deductions));
    if (p.absent_deductions > 0)    rows.push(row('Absent',              p.absent_deductions));
    if (p.other_deductions > 0)     rows.push(row('Other Deductions',    p.other_deductions));
    if (p.deferred_balance > 0)     rows.push(row('Deferred Balance',    p.deferred_balance));
    return rows.join('');
}

function calcMandatoryTotal(p) {
    return (p.sss || 0) + (p.philhealth || 0) + (p.pagibig || 0) +
           (p.withholding_tax || 0) + (p.late_deductions || 0) +
           (p.undertime_deductions || 0) + (p.absent_deductions || 0) +
           (p.other_deductions || 0) + (p.deferred_balance || 0);
}

// ── Loan deductions — named per loan from DB ──────────────────────────────────
function buildLoanDeductions(p) {
    const rows  = [];
    let   total = 0;

    if (Array.isArray(p.loan_deductions) && p.loan_deductions.length > 0) {
        p.loan_deductions.forEach(ld => {
            if (ld.amount > 0) {
                rows.push(row(ld.label, ld.amount));
                total += ld.amount;
            }
        });
    }

    return { html: rows.join(''), total };
}

function row(label, amount) {
    return `<tr class="border-bottom">
        <td class="text-muted small font-weight-bold text-uppercase py-2 ps-3 border-0">${esc(label)}</td>
        <td class="text-end font-weight-bold text-secondary py-2 pe-3 border-0">${peso(amount)}</td>
    </tr>`;
}

// ── Download stub ─────────────────────────────────────────────────────────────
function downloadPayslip(payslip) {
    if (!payslip) return;
    Swal.fire({
        icon: 'info',
        title: 'Coming Soon',
        text: `PDF download for ${payslip.period} will be available soon.`,
        confirmButtonColor: '#1a1a1a',
    });
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function peso(amount) {
    return '₱' + Number(amount || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const [y, m, d] = dateStr.split('-');
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return `${months[parseInt(m) - 1]} ${parseInt(d)}, ${y}`;
}

function periodTypeLabel(type) {
    return type === '1st-15th' ? '1st–15th' : '16th–End';
}

function esc(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str ?? ''));
    return d.innerHTML;
}
</script>
@endpush