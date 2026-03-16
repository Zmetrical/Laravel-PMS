@extends('layouts.main')

@section('title', 'HR Reports')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">HR Reports</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">HR Reports</h4>
    </div>
    <div class="d-flex gap-3 flex-wrap align-items-center">
        <div class="input-group shadow-sm" style="width:200px">
            <span class="input-group-text bg-white border-end-0 text-muted font-weight-bold text-uppercase" style="font-size: 0.75rem;">Period</span>
            <input type="month" id="selectedMonth" class="form-control border-start-0 font-weight-bold text-dark ps-0">
        </div>
        <select id="cutoffPeriod" class="form-select shadow-sm font-weight-bold text-dark" style="width:220px">
            <option value="full">Full Month</option>
            <option value="first">1st Cutoff (1–15)</option>
            <option value="second">2nd Cutoff (16–end)</option>
        </select>
    </div>
</div>

{{-- Category Filters --}}
<div class="d-flex flex-wrap gap-2 mb-4 pb-3 border-bottom" id="categoryFilters"></div>

{{-- Report Cards --}}
<div class="row g-4" id="reportCardsContainer"></div>

<div id="noResults" class="text-center py-5 bg-white border rounded shadow-sm d-none mt-3">
    <span class="font-weight-bold text-dark d-block mb-1">No Reports Found</span>
    <small class="text-muted font-weight-bold text-uppercase">Try adjusting your category filter.</small>
</div>

{{-- ===== MODAL: PREVIEW ===== --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3 d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="modal-title font-weight-bold mb-1 text-dark text-uppercase" id="previewModalLabel">—</h6>
                    <div class="text-muted small font-weight-bold text-uppercase" id="previewModalPeriod" style="font-size: 0.7rem;"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-0 bg-light" id="reportPreviewArea">
                <div class="text-center py-5">
                    <span class="spinner-border spinner-border-sm text-secondary me-2"></span>
                    <span class="font-weight-bold text-muted text-uppercase small">Generating Report...</span>
                </div>
            </div>
            
            <div class="modal-footer bg-white py-3 d-flex justify-content-between">
                <button class="btn btn-outline-dark font-weight-bold px-4 shadow-sm" onclick="printPreview()">
                    <i class="bi bi-printer me-2"></i>Print
                </button>
                <div class="d-flex gap-3">
                    <button class="btn btn-secondary font-weight-bold px-4 shadow-sm" onclick="exportCSV()">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export CSV
                    </button>
                    <button class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .report-card { cursor: pointer; transition: all 0.2s ease-in-out; }
    .report-card:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; border-color: #6c757d !important; }
    #previewModal .modal-dialog { max-width: 95vw; }
    
    /* Ensure the generated tables in the modal match the app styling */
    #reportPreviewArea table { background: #fff; margin-bottom: 0; }
    #reportPreviewArea thead th { background: #f8f9fa; color: #6c757d; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; padding: 1rem 0.75rem; border-bottom: 2px solid #dee2e6; white-space: nowrap; }
    #reportPreviewArea tbody td { padding: 1rem 0.75rem; vertical-align: middle; border-bottom: 1px solid #dee2e6; font-size: 0.85rem; white-space: nowrap; }
    #reportPreviewArea tfoot td { background: #f8f9fa; font-weight: 700; color: #343a40; padding: 1rem 0.75rem; }

    @media print {
        @page { size: landscape; margin: 1cm; }
        body * { visibility: hidden; }
        #previewModal, #previewModal * { visibility: visible; }
        #previewModal { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
        .modal-header, .modal-footer, .btn-close { display: none !important; }
        #reportPreviewArea { overflow: visible !important; max-height: none !important; padding: 0 !important; }
        #reportPreviewArea table { width: 100%; border-collapse: collapse; font-size: 10px; }
        #reportPreviewArea th, #reportPreviewArea td { border: 1px solid #000; padding: 4px !important; white-space: normal; word-wrap: break-word; }
        #reportPreviewArea thead { display: table-header-group; }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    'use strict';

    // ─── CONFIG ──────────────────────────────────────────────────────────────
    const BASE = '{{ url("/hresource/reports") }}';

    // ─── REPORT DEFINITIONS ──────────────────────────────────────────────────
    const REPORTS = [
        {
            id: 'employee-masterlist',
            title: 'Employee Master List',
            description: 'Complete employee roster with personal and employment details.',
            category: 'analytics', categoryLabel: 'Analytics',
            requirement: 'DOLE', frequency: 'As needed',
        },
        {
            id: 'dtr',
            title: 'Daily Time Record Summary',
            description: 'Attendance, tardiness, undertime, and absence records per cutoff.',
            category: 'attendance', categoryLabel: 'Attendance',
            requirement: 'DOLE', frequency: 'Daily / Monthly',
        },
        {
            id: 'payroll-register',
            title: 'Payroll Register',
            description: 'Full payroll summary with gross earnings, deductions, and net pay.',
            category: 'payroll', categoryLabel: 'Payroll',
            requirement: 'Internal / DOLE', frequency: 'Per payroll period',
        },
        {
            id: 'sss-loans',
            title: 'SSS Loans Summary',
            description: 'All SSS loans with amortization schedule and outstanding balances.',
            category: 'payroll', categoryLabel: 'Payroll',
            requirement: 'SSS', frequency: 'As needed',
        },
        {
            id: 'pagibig-loans',
            title: 'Pag-IBIG Loans Summary',
            description: 'All Pag-IBIG loans with amortization schedule and outstanding balances.',
            category: 'payroll', categoryLabel: 'Payroll',
            requirement: 'Pag-IBIG', frequency: 'As needed',
        },
    ];

    const CATEGORIES = [
        { value: 'all',        label: 'All Reports' },
        { value: 'payroll',    label: 'Payroll'     },
        { value: 'attendance', label: 'Attendance'  },
        { value: 'analytics',  label: 'Analytics'   },
    ];

    // ─── STATE ────────────────────────────────────────────────────────────────
    let activeCategory     = 'all';
    let currentReportId    = null;
    let currentData        = [];
    let currentPeriodLabel = '';

    // ─── INIT ─────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const now = new Date();
        document.getElementById('selectedMonth').value =
            now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');

        renderCategoryFilters();
        renderCards();
    });

    // ─── CATEGORY FILTERS ─────────────────────────────────────────────────────
    function renderCategoryFilters() {
        document.getElementById('categoryFilters').innerHTML = CATEGORIES.map(function (c) {
            const active = activeCategory === c.value;
            return `<button type="button" class="btn font-weight-bold shadow-sm ${active ? 'btn-secondary' : 'btn-white border text-muted'}" 
                onclick="setCategory('${c.value}')">${c.label}</button>`;
        }).join('');
    }

    window.setCategory = function (val) {
        activeCategory = val;
        renderCategoryFilters();
        renderCards();
    };

    // ─── REPORT CARDS ─────────────────────────────────────────────────────────
    function renderCards() {
        const container = document.getElementById('reportCardsContainer');
        const noRes     = document.getElementById('noResults');

        const filtered = REPORTS.filter(function (r) {
            return activeCategory === 'all' || r.category === activeCategory;
        });

        if (!filtered.length) {
            container.innerHTML = '';
            noRes.classList.remove('d-none');
            return;
        }
        noRes.classList.add('d-none');

        container.innerHTML = filtered.map(function (r) {
            return `
            <div class="col-md-6 col-xl-4">
                <div class="card report-card h-100 border-0 shadow-sm" onclick="openReport('${r.id}')">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-light border text-dark text-uppercase px-2 py-1">${r.categoryLabel}</span>
                        </div>
                        <h6 class="font-weight-bold text-dark text-uppercase mb-2">${x(r.title)}</h6>
                        <p class="text-muted small font-weight-bold mb-4">${x(r.description)}</p>
                        
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-end">
                            <div>
                                <div class="text-muted text-uppercase font-weight-bold" style="font-size: 0.65rem;">Required By</div>
                                <div class="text-dark font-weight-bold small">${x(r.requirement)}</div>
                            </div>
                            <div class="text-end">
                                <div class="text-muted text-uppercase font-weight-bold" style="font-size: 0.65rem;">Frequency</div>
                                <div class="text-dark font-weight-bold small">${x(r.frequency)}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    // ─── OPEN REPORT ──────────────────────────────────────────────────────────
    window.openReport = function (reportId) {
        const monthVal = document.getElementById('selectedMonth').value;
        const cutoff   = document.getElementById('cutoffPeriod').value;

        if (!monthVal) {
            Swal.fire({ icon: 'warning', title: 'Select a Period', text: 'Please select a month before generating a report.', confirmButtonColor: '#1a1a1a' });
            return;
        }

        const [y, m] = monthVal.split('-').map(Number);
        const lastDay = new Date(y, m, 0).getDate();
        const mName   = new Date(y, m - 1).toLocaleString('en-PH', { month: 'long' });

        if      (cutoff === 'first')  currentPeriodLabel = `${mName} 1–15, ${y}`;
        else if (cutoff === 'second') currentPeriodLabel = `${mName} 16–${lastDay}, ${y}`;
        else                          currentPeriodLabel = `${mName} ${y}`;

        currentReportId = reportId;
        const report    = REPORTS.find(function (r) { return r.id === reportId; });

        document.getElementById('previewModalLabel').textContent  = report.title;
        document.getElementById('previewModalPeriod').textContent = `Period: ${currentPeriodLabel}`;
        
        document.getElementById('reportPreviewArea').innerHTML = `
            <div class="text-center py-5">
                <span class="spinner-border spinner-border-sm text-secondary me-2"></span>
                <span class="font-weight-bold text-muted text-uppercase small">Generating Report...</span>
            </div>`;

        bootstrap.Modal.getOrCreateInstance(document.getElementById('previewModal')).show();

        fetchReport(reportId, y, m, cutoff)
            .then(function (data) {
                currentData = data;
                document.getElementById('reportPreviewArea').innerHTML = buildPreview(reportId, data, currentPeriodLabel);
            })
            .catch(function (err) {
                document.getElementById('reportPreviewArea').innerHTML = `
                    <div class="text-center py-5 bg-white m-4 border rounded shadow-sm">
                        <span class="text-dark font-weight-bold d-block mb-1">Failed to load report</span>
                        <small class="text-muted font-weight-bold text-uppercase">${err && err.message ? err.message : 'Please try again.'}</small>
                    </div>`;
            });
    };

    // ─── FETCH ────────────────────────────────────────────────────────────────
    function fetchReport(reportId, year, month, cutoff) {
        const params = new URLSearchParams({ year, month, cutoff });

        const urlMap = {
            'employee-masterlist': BASE + '/employee-masterlist',
            'dtr':                 BASE + '/dtr?' + params,
            'payroll-register':    BASE + '/payroll-register?' + params,
            'sss-loans':           BASE + '/loans?type=sss',
            'pagibig-loans':       BASE + '/loans?type=pagibig',
        };

        const url = urlMap[reportId];
        if (!url) return Promise.reject(new Error('Unknown report.'));

        return fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) {
                return r.json().then(function (data) {
                    if (!r.ok) return Promise.reject(data);
                    return data;
                });
            });
    }

    // ─── PREVIEW BUILDERS ─────────────────────────────────────────────────────

    function peso(n) {
        return '\u20b1' + parseFloat(('' + n).replace(/,/g, '') || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function sumCol(arr, key) {
        return arr.reduce(function (a, r) {
            return a + parseFloat(('' + (r[key] || 0)).replace(/,/g, ''));
        }, 0);
    }

    function emptyState(msg) {
        return `<div class="text-center py-5 bg-white m-4 border rounded shadow-sm text-muted font-weight-bold text-uppercase">${x(msg)}</div>`;
    }

    function buildPreview(id, data, periodLabel) {
        switch (id) {
            case 'employee-masterlist': return buildEmployeeMasterlist(data);
            case 'dtr':                 return buildDTR(data);
            case 'payroll-register':    return buildPayrollRegister(data);
            case 'sss-loans':           return buildLoans(data, 'SSS');
            case 'pagibig-loans':       return buildLoans(data, 'PAG-IBIG');
            default: return `<p class="text-muted p-4 font-weight-bold text-center">No preview available.</p>`;
        }
    }

    // Employee Master List
    function buildEmployeeMasterlist(data) {
        if (!data.length) return emptyState('No active employees found.');

        const rows = data.map(function (e) {
            const statusClass = e.employment_status.toLowerCase() === 'active' ? 'bg-secondary text-white' : 'bg-light border text-muted';
            return `
            <tr>
                <td class="ps-4 font-weight-bold text-dark">${x(e.employee_id)}</td>
                <td class="font-weight-bold text-dark">${x(e.full_name)}</td>
                <td>${x(e.position)}</td>
                <td>${x(e.department)}</td>
                <td>${x(e.branch)}</td>
                <td><span class="badge ${statusClass} px-2 py-1 text-uppercase">${x(e.employment_status)}</span></td>
                <td class="text-secondary font-weight-bold">${x(e.hire_date)}</td>
                <td>${x(e.gender)}</td>
                <td>${x(e.email)}</td>
                <td class="pe-4">${x(e.phone)}</td>
            </tr>`;
        }).join('');

        return `
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Emp ID</th>
                        <th>Full Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Branch</th>
                        <th>Status</th>
                        <th>Hire Date</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th class="pe-4">Phone</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
                <tfoot>
                    <tr>
                        <td colspan="10" class="text-end pe-4">Total: ${data.length} employee(s)</td>
                    </tr>
                </tfoot>
            </table>
        </div>`;
    }

    // DTR Summary
    function buildDTR(data) {
        if (!data.length) return emptyState('No attendance records found for this period.');

        const rows = data.map(function (r) {
            const statusClass = {
                present: 'bg-secondary text-white',
                late: 'bg-light border text-dark',
                absent: 'bg-light border border-secondary text-muted',
                leave: 'bg-white border border-dark text-dark'
            }[r.status.toLowerCase()] || 'bg-light border text-dark';

            return `
            <tr>
                <td class="ps-4 font-weight-bold text-dark">${x(r.employee_id)}</td>
                <td class="font-weight-bold text-dark">${x(r.employee)}</td>
                <td>${x(r.department)}</td>
                <td class="text-secondary font-weight-bold">${x(r.date)}</td>
                <td class="text-secondary font-weight-bold">${x(r.time_in)}</td>
                <td class="text-secondary font-weight-bold">${x(r.time_out)}</td>
                <td class="text-end font-weight-bold text-dark">${x(r.hours_worked)}</td>
                <td class="text-end text-dark">${r.late_minutes}</td>
                <td class="text-end text-dark">${r.undertime_minutes}</td>
                <td class="text-end font-weight-bold text-dark">${x(r.overtime_hours)}</td>
                <td class="pe-4 text-center"><span class="badge ${statusClass} px-2 py-1 text-uppercase">${x(r.status.replace('_', ' '))}</span></td>
            </tr>`;
        }).join('');

        return `
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Emp ID</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th class="text-end">Hrs</th>
                        <th class="text-end">Late (min)</th>
                        <th class="text-end">UT (min)</th>
                        <th class="text-end">OT (hrs)</th>
                        <th class="text-center pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
                <tfoot>
                    <tr>
                        <td colspan="11" class="text-end pe-4">Total Records: ${data.length}</td>
                    </tr>
                </tfoot>
            </table>
        </div>`;
    }

    // Payroll Register
    function buildPayrollRegister(data) {
        if (!data.length) return emptyState('No released payroll records found for this period.');

        const rows = data.map(function (p) {
            const lateUT = parseFloat((p.late_deductions + '').replace(/,/g, '')) + parseFloat((p.undertime_deductions + '').replace(/,/g, ''));
            return `
            <tr>
                <td class="ps-4 font-weight-bold text-dark">${x(p.employee_id)}</td>
                <td class="font-weight-bold text-dark">${x(p.employee)}</td>
                <td class="border-end">${x(p.department)}</td>
                
                <td class="text-end text-secondary">${peso(p.basic_pay)}</td>
                <td class="text-end text-secondary">${peso(p.overtime_pay)}</td>
                <td class="text-end text-secondary">${peso(p.night_diff_pay)}</td>
                <td class="text-end text-secondary">${peso(p.holiday_pay)}</td>
                <td class="text-end text-secondary">${peso(p.allowances)}</td>
                <td class="text-end font-weight-bold text-dark border-end">${peso(p.gross_pay)}</td>
                
                <td class="text-end text-danger">${peso(p.sss)}</td>
                <td class="text-end text-danger">${peso(p.philhealth)}</td>
                <td class="text-end text-danger">${peso(p.pagibig)}</td>
                <td class="text-end text-danger">${peso(p.withholding_tax)}</td>
                <td class="text-end text-danger">${peso(lateUT)}</td>
                <td class="text-end text-danger">${peso(p.absent_deductions)}</td>
                <td class="text-end text-danger">${peso(p.other_deductions)}</td>
                <td class="text-end font-weight-bold text-danger border-end">${peso(p.total_deductions)}</td>
                
                <td class="text-end font-weight-bold text-success pe-4">${peso(p.net_pay)}</td>
            </tr>`;
        }).join('');

        const lateUTTotal = sumCol(data, 'late_deductions') + sumCol(data, 'undertime_deductions');

        return `
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th rowspan="2" class="ps-4 align-bottom border-bottom">Emp ID</th>
                        <th rowspan="2" class="align-bottom border-bottom">Employee</th>
                        <th rowspan="2" class="align-bottom border-bottom border-end">Dept</th>
                        <th colspan="6" class="text-center border-end border-bottom-0">EARNINGS</th>
                        <th colspan="7" class="text-center border-end border-bottom-0">DEDUCTIONS</th>
                        <th rowspan="2" class="text-end pe-4 align-bottom border-bottom">NET PAY</th>
                    </tr>
                    <tr>
                        <th class="text-end border-bottom">Basic</th>
                        <th class="text-end border-bottom">OT</th>
                        <th class="text-end border-bottom">Night Diff</th>
                        <th class="text-end border-bottom">Holiday</th>
                        <th class="text-end border-bottom">Allow.</th>
                        <th class="text-end border-bottom border-end">Gross</th>
                        
                        <th class="text-end border-bottom">SSS</th>
                        <th class="text-end border-bottom">PhilHlth</th>
                        <th class="text-end border-bottom">Pag-IBIG</th>
                        <th class="text-end border-bottom">Tax</th>
                        <th class="text-end border-bottom">Late/UT</th>
                        <th class="text-end border-bottom">Absent</th>
                        <th class="text-end border-bottom">Other</th>
                        <th class="text-end border-bottom border-end">Total Ded.</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end pe-3 border-end">TOTALS</td>
                        <td class="text-end">${peso(sumCol(data, 'basic_pay'))}</td>
                        <td class="text-end">${peso(sumCol(data, 'overtime_pay'))}</td>
                        <td class="text-end">${peso(sumCol(data, 'night_diff_pay'))}</td>
                        <td class="text-end">${peso(sumCol(data, 'holiday_pay'))}</td>
                        <td class="text-end">${peso(sumCol(data, 'allowances'))}</td>
                        <td class="text-end border-end">${peso(sumCol(data, 'gross_pay'))}</td>
                        
                        <td class="text-end">${peso(sumCol(data, 'sss'))}</td>
                        <td class="text-end">${peso(sumCol(data, 'philhealth'))}</td>
                        <td class="text-end">${peso(sumCol(data, 'pagibig'))}</td>
                        <td class="text-end">${peso(sumCol(data, 'withholding_tax'))}</td>
                        <td class="text-end">${peso(lateUTTotal)}</td>
                        <td class="text-end">${peso(sumCol(data, 'absent_deductions'))}</td>
                        <td class="text-end">${peso(sumCol(data, 'other_deductions'))}</td>
                        <td class="text-end border-end">${peso(sumCol(data, 'total_deductions'))}</td>
                        
                        <td class="text-end pe-4">${peso(sumCol(data, 'net_pay'))}</td>
                    </tr>
                </tfoot>
            </table>
        </div>`;
    }

    // Loans (SSS or PAG-IBIG)
    function buildLoans(data, type) {
        if (!data.length) return emptyState(`No ${type} loans found.`);

        const totalBal = sumCol(data, 'remaining_balance');
        const totalAmt = sumCol(data, 'amount');

        const rows = data.map(function (l) {
            const statusClass = l.status.toLowerCase() === 'active' ? 'bg-secondary text-white' : 'bg-light border text-muted';
            return `
            <tr>
                <td class="ps-4 font-weight-bold text-dark">${x(l.employee_id)}</td>
                <td class="font-weight-bold text-dark">${x(l.employee)}</td>
                <td>${x(l.department)}</td>
                <td class="text-end font-weight-bold text-dark">${peso(l.amount)}</td>
                <td class="text-end text-secondary">${peso(l.monthly_amortization)}</td>
                <td class="text-center font-weight-bold text-dark">${l.term_months}</td>
                <td class="text-secondary font-weight-bold">${x(l.start_date)}</td>
                <td class="text-center font-weight-bold text-dark">${l.payments_made} / ${l.term_months}</td>
                <td class="text-end font-weight-bold text-danger">${peso(l.remaining_balance)}</td>
                <td class="text-center pe-4"><span class="badge ${statusClass} px-2 py-1 text-uppercase">${x(l.status)}</span></td>
            </tr>`;
        }).join('');

        return `
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Emp ID</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th class="text-end">Loan Amt</th>
                        <th class="text-end">Monthly</th>
                        <th class="text-center">Term</th>
                        <th>Start Date</th>
                        <th class="text-center">Payments</th>
                        <th class="text-end">Balance</th>
                        <th class="text-center pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end pe-3">TOTALS</td>
                        <td class="text-end">${peso(totalAmt)}</td>
                        <td colspan="4"></td>
                        <td class="text-end">${peso(totalBal)}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>`;
    }

    // ─── PRINT ────────────────────────────────────────────────────────────────
    window.printPreview = function () {
        const content = document.getElementById('reportPreviewArea').innerHTML;
        const win = window.open('', '_blank');
        if (!win) { alert('Please allow pop-ups to use this feature.'); return; }
        
        const title = document.getElementById('previewModalLabel').textContent;
        const period = document.getElementById('previewModalPeriod').textContent;

        win.document.write(`
        <!DOCTYPE html><html><head>
            <title>HR Report – ${title}</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
            <style>
                body { padding: 20px; font-family: system-ui, -apple-system, sans-serif; }
                h4 { text-align: center; margin-bottom: 5px; font-weight: bold; text-transform: uppercase; }
                p { text-align: center; margin-bottom: 20px; color: #6c757d; font-size: 14px; }
                table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 20px; }
                th, td { border: 1px solid #000; padding: 6px !important; white-space: nowrap; }
                thead th { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; text-transform: uppercase; }
                tfoot td { font-weight: bold; background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
                .text-end { text-align: right; }
                .text-center { text-align: center; }
                @page { size: landscape; margin: 1cm; }
            </style>
        </head><body>
            <h4>${title}</h4>
            <p>${period}</p>
            ${content}
        </body></html>`);
        
        win.document.close();
        win.onload = function () { win.focus(); setTimeout(function () { win.print(); win.close(); }, 500); };
    };

    // ─── EXPORT CSV ───────────────────────────────────────────────────────────
    window.exportCSV = function () {
        if (!currentData || !currentData.length) {
            Swal.fire({ icon: 'info', title: 'No Data', text: 'Nothing to export for the selected period.', confirmButtonColor: '#1a1a1a' });
            return;
        }
        const headers = Object.keys(currentData[0]).map(function (k) {
            return k.replace(/_/g, ' ').replace(/\b\w/g, function (l) { return l.toUpperCase(); });
        });
        const rows = currentData.map(function (row) {
            return Object.values(row).map(function (v) {
                if (v === null || v === undefined) return '';
                const s = String(v);
                return (s.includes(',') || s.includes('"') || s.includes('\n'))
                    ? '"' + s.replace(/"/g, '""') + '"' : s;
            }).join(',');
        });
        const csv  = '\uFEFF' + [headers.join(',')].concat(rows).join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = currentReportId + '_' + document.getElementById('selectedMonth').value + '.csv';
        a.click();
        URL.revokeObjectURL(url);
    };

    // ─── UTIL ─────────────────────────────────────────────────────────────────
    function x(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

})();
</script>
@endpush