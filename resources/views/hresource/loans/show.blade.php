@extends('layouts.main')

@section('title', 'Loan Management')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">Loan Management</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Loan Management</h4>
        <small class="text-muted font-weight-bold text-uppercase">Manage SSS and PAG-IBIG loan deductions</small>
    </div>
    <button class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm" onclick="openAddModal()">
        Add Loan
    </button>
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-5">
    @foreach ([
        ['id' => 'statActive',   'label' => 'Active Loans'],
        ['id' => 'statCompleted','label' => 'Completed'],
        ['id' => 'statSSS',      'label' => 'SSS Loans'],
        ['id' => 'statPagibig',  'label' => 'PAG-IBIG Loans'],
        ['id' => 'statBalance',  'label' => 'Total Balance'],
    ] as $c)
    <div class="col-6 col-md-4 col-lg">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">{{ $c['label'] }}</span>
            <span class="h4 font-weight-bold text-dark mb-0" id="{{ $c['id'] }}">
                <span class="spinner-border spinner-border-sm text-secondary"></span>
            </span>
        </div>
    </div>
    @endforeach
</div>

{{-- Table Card --}}
<div class="card shadow-sm border-0 mb-5">
    
    {{-- Tabs --}}
    <div class="card-header bg-white p-0 border-bottom-0">
        <ul class="nav nav-tabs px-4 pt-3 border-bottom" id="loanTabs">
            <li class="nav-item">
                <a class="nav-link active font-weight-bold text-uppercase py-3 text-secondary" href="#" data-status="active" onclick="setTab(this); return false;">
                    Active <span class="badge bg-secondary ms-2" id="tabActive">—</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold text-uppercase py-3 text-secondary" href="#" data-status="completed" onclick="setTab(this); return false;">
                    Completed <span class="badge bg-secondary ms-2" id="tabCompleted">—</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold text-uppercase py-3 text-secondary" href="#" data-status="all" onclick="setTab(this); return false;">
                    All <span class="badge bg-secondary ms-2" id="tabAll">—</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- Filters --}}
    <div class="card-body bg-light border-bottom p-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control border-start-0 font-weight-bold text-dark ps-0"
                        placeholder="Search by name or loan ID…" oninput="debounceLoad()">
                </div>
            </div>
            <div class="col-md-4">
                <select id="typeFilter" class="form-select shadow-sm font-weight-bold text-dark" onchange="loadList()">
                    <option value="all">All Loan Types</option>
                    <option value="sss">SSS Loan</option>
                    <option value="pagibig">PAG-IBIG Loan</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 ps-4 py-3">Loan ID</th>
                        <th class="border-0 py-3">Employee</th>
                        <th class="border-0 py-3">Type</th>
                        <th class="border-0 py-3 text-end">Amount</th>
                        <th class="border-0 py-3 text-end">Monthly</th>
                        <th class="border-0 py-3" style="min-width:140px">Progress</th>
                        <th class="border-0 py-3 text-end">Balance</th>
                        <th class="border-0 py-3 text-center">Status</th>
                        <th class="border-0 py-3 text-center pe-4" style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="loanTableBody">
                    <tr>
                        <td colspan="9" class="text-center py-5 bg-white text-muted font-weight-bold">
                            <span class="spinner-border spinner-border-sm me-2 text-secondary"></span>Loading Loans…
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== MODAL: ADD ===== --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase">Add New Loan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">

                <div class="bg-white border rounded p-4 shadow-sm mb-4">
                    {{-- Employee Search --}}
                    <div class="mb-4">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Employee <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="addEmpSearch" class="form-control border-start-0 font-weight-bold text-dark ps-0"
                                    placeholder="Search by name, ID, or department…" autocomplete="off" oninput="searchEmployees()">
                            </div>
                            <div id="addEmpDropdown" class="list-group position-absolute w-100 shadow z-3 d-none border-secondary"
                                style="max-height:200px;overflow-y:auto;top:100%;"></div>
                        </div>
                        <input type="hidden" id="addEmpId">
                        <div id="addEmpSelected" class="mt-3 d-none">
                            <span class="badge bg-secondary py-2 px-3 text-uppercase shadow-sm" id="addEmpSelectedName"></span>
                        </div>
                    </div>

                    {{-- Loan Type --}}
                    <div class="mb-0">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Loan Type <span class="text-danger">*</span></label>
                        <select id="addLoanType" class="form-select border-secondary shadow-sm font-weight-bold text-dark">
                            <option value="">Select loan type</option>
                            <option value="sss">SSS Loan</option>
                            <option value="pagibig">PAG-IBIG Loan</option>
                        </select>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    {{-- Amount + Amortization --}}
                    <div class="col-md-6">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Total Loan Amount <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light text-dark font-weight-bold">&#8369;</span>
                            <input type="number" id="addAmount" class="form-control font-weight-bold text-dark"
                                placeholder="0.00" min="1" step="100" oninput="autoCalcAmortization()">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                            Monthly Amortization <span class="text-danger">*</span>
                        </label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light text-dark font-weight-bold">&#8369;</span>
                            <input type="number" id="addAmortization" class="form-control font-weight-bold text-dark"
                                placeholder="0.00" min="1" step="0.01">
                        </div>
                    </div>

                    {{-- Start Date + Term --}}
                    <div class="col-md-6">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Start Date <span class="text-danger">*</span></label>
                        <input type="date" id="addStartDate" class="form-control shadow-sm font-weight-bold text-dark">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Number of Payments <span class="text-danger">*</span></label>
                        <select id="addTerm" class="form-select shadow-sm font-weight-bold text-dark" onchange="autoCalcAmortization()">
                            <option value="12">12 months (1 year)</option>
                            <option value="18">18 months (1.5 years)</option>
                            <option value="24" selected>24 months (2 years)</option>
                            <option value="36">36 months (3 years)</option>
                            <option value="48">48 months (4 years)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Notes</label>
                    <textarea id="addNotes" class="form-control shadow-sm p-3" rows="2" placeholder="Optional notes…"></textarea>
                </div>

                {{-- Summary Preview --}}
                <div id="addSummary" class="border border-secondary rounded p-3 bg-white shadow-sm d-none text-center">
                    <p class="text-dark small font-weight-bold text-uppercase border-bottom pb-2 mb-3">Loan Summary</p>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Total</div>
                            <span class="font-weight-bold text-dark" id="sumAmount"></span>
                        </div>
                        <div class="col-6 col-md-3 border-start border-end">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Monthly</div>
                            <span class="font-weight-bold text-dark" id="sumMonthly"></span>
                        </div>
                        <div class="col-6 col-md-3 border-end">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Term</div>
                            <span class="font-weight-bold text-dark" id="sumTerm"></span>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Total Pay</div>
                            <span class="font-weight-bold text-dark" id="sumTotal"></span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-white py-3">
                <button class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-secondary font-weight-bold px-4 shadow-sm" id="addSubmitBtn" onclick="submitAddLoan()">Add Loan</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL: EDIT ===== --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase">Edit Loan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="editLoanId">

                <div class="border rounded p-3 bg-white shadow-sm mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <strong class="text-dark text-uppercase font-weight-bold" id="editEmpName"></strong>
                        <span class="text-muted small font-weight-bold mx-2">|</span>
                        <span id="editLoanTypeName" class="badge bg-light border text-dark"></span>
                    </div>
                    <div class="text-muted small font-weight-bold text-uppercase">
                        Payments made: <span id="editPaymentsMade" class="text-dark ms-1"></span>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Total Loan Amount <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light text-dark font-weight-bold">&#8369;</span>
                            <input type="number" id="editAmount" class="form-control font-weight-bold text-dark" min="1" step="100" oninput="updateEditSummary()">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Monthly Amortization <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light text-dark font-weight-bold">&#8369;</span>
                            <input type="number" id="editAmortization" class="form-control font-weight-bold text-dark" min="1" step="0.01" oninput="updateEditSummary()">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Start Date <span class="text-danger">*</span></label>
                        <input type="date" id="editStartDate" class="form-control shadow-sm font-weight-bold text-dark">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Number of Payments <span class="text-danger">*</span></label>
                        <select id="editTerm" class="form-select shadow-sm font-weight-bold text-dark">
                            <option value="12">12 months</option>
                            <option value="18">18 months</option>
                            <option value="24">24 months</option>
                            <option value="36">36 months</option>
                            <option value="48">48 months</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Notes</label>
                    <textarea id="editNotes" class="form-control shadow-sm p-3" rows="2"></textarea>
                </div>

                <div id="editSummary" class="border border-secondary rounded p-3 bg-white shadow-sm d-none text-center">
                    <p class="text-dark small font-weight-bold text-uppercase border-bottom pb-2 mb-3">Recalculated Balance</p>
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Payments Made</div>
                            <span class="font-weight-bold text-dark" id="editSumPaid"></span>
                        </div>
                        <div class="col-4 border-start border-end">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Total Paid</div>
                            <span class="font-weight-bold text-dark" id="editSumTotalPaid"></span>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">New Balance</div>
                            <span class="font-weight-bold text-dark" id="editSumBalance"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white py-3">
                <button class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-secondary font-weight-bold px-4 shadow-sm" onclick="submitEditLoan()">Update Loan</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL: VIEW ===== --}}
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase">Loan Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light" id="viewModalBody">
                <div class="text-center py-5">
                    <span class="spinner-border spinner-border-sm text-secondary"></span>
                </div>
            </div>
            <div class="modal-footer bg-white py-3">
                <button class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL: DELETE ===== --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase">Delete Loan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light text-center">
                <p class="text-dark font-weight-bold mb-3">This action cannot be undone.</p>
                <div class="border rounded p-3 small bg-white shadow-sm text-start" id="deleteInfo"></div>
            </div>
            <div class="modal-footer bg-white py-3 d-flex justify-content-between">
                <button class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-secondary font-weight-bold px-4 shadow-sm" id="confirmDeleteBtn" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ─── CONFIG ──────────────────────────────────────────────────────────────
    const CSRF = '{{ csrf_token() }}';
    const BASE = '{{ url("/hresource/loans") }}';

    // ─── STATE ───────────────────────────────────────────────────────────────
    let currentStatus  = 'active';
    let deleteLoanId   = null;
    let editPayments   = 0;
    let empSearchTimer = null;

    // ─── INIT ────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        loadStats();
        loadList();

        document.addEventListener('click', function (e) {
            const dd = document.getElementById('addEmpDropdown');
            if (dd && !dd.contains(e.target) && e.target.id !== 'addEmpSearch') {
                dd.classList.add('d-none');
            }
        });
    });

    // ─── STATS ───────────────────────────────────────────────────────────────
    function loadStats() {
        fetch(BASE + '/stats')
            .then(r => r.json())
            .then(function (d) {
                setText('statActive',    d.active);
                setText('statCompleted', d.completed);
                setText('statSSS',       d.sss);
                setText('statPagibig',   d.pagibig);
                setText('statBalance',   '\u20b1' + d.total_balance);
            })
            .catch(console.error);
    }

    // ─── LIST ─────────────────────────────────────────────────────────────────
    window.loadList = function () {
        const tbody  = document.getElementById('loanTableBody');
        const search = document.getElementById('searchInput').value;
        const type   = document.getElementById('typeFilter').value;

        tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 bg-white text-muted font-weight-bold"><span class="spinner-border spinner-border-sm me-2 text-secondary"></span>Loading Loans…</td></tr>`;

        const url = BASE + '/list?status=' + currentStatus + '&type=' + type + '&search=' + encodeURIComponent(search);

        fetch(url)
            .then(r => r.json())
            .then(function (data) {
                updateTabCounts(data);
                renderTable(data);
            })
            .catch(function () {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 bg-white text-dark font-weight-bold">Failed to load data.</td></tr>`;
            });
    };

    function renderTable(data) {
        const tbody = document.getElementById('loanTableBody');

        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 bg-white text-muted font-weight-bold">No loans found.</td></tr>`;
            return;
        }

        tbody.innerHTML = data.map(function (l) {
            const typeBadge = `<span class="badge ${l.loan_type === 'sss' ? 'bg-secondary text-white' : 'bg-light border text-dark'} px-2 py-1 text-uppercase">${x(l.loan_type_name)}</span>`;

            const statusBadge = l.status === 'active'
                ? `<span class="badge bg-secondary px-2 py-1 text-uppercase">Active</span>`
                : `<span class="badge bg-light border text-muted px-2 py-1 text-uppercase">Completed</span>`;

            const progress = `
                <div class="d-flex justify-content-between mb-1 small font-weight-bold text-uppercase text-muted" style="font-size: 0.65rem;">
                    <span>${l.payments_made}/${l.term_months}</span>
                    <span>${l.progress_percent}%</span>
                </div>
                <div class="progress bg-light border" style="height:6px">
                    <div class="progress-bar bg-secondary" style="width:${l.progress_percent}%"></div>
                </div>`;

            const balance = parseFloat(l.remaining_balance.replace(/,/g, ''));
            const balanceCell = `<span class="font-weight-bold ${balance <= 0 ? 'text-muted' : 'text-dark'}">\u20b1${x(l.remaining_balance)}</span>`;

            let actions = `<button class="btn btn-sm btn-light border text-dark font-weight-bold shadow-sm px-3 me-1" title="View" onclick="openViewModal(${l.id})">View</button>`;

            if (l.status === 'active') {
                actions += `<button class="btn btn-sm btn-outline-dark font-weight-bold px-2 me-1" title="Edit" onclick="openEditModal(${l.id})"><i class="bi bi-pencil-fill"></i></button>
                            <button class="btn btn-sm btn-outline-dark font-weight-bold px-2" title="Delete" onclick="openDeleteModal(${l.id}, '${x(l.employee)}', '${x(l.loan_type_name)}', '${x(l.remaining_balance)}')"><i class="bi bi-trash-fill"></i></button>`;
            }

            return `
                <tr class="border-bottom bg-white">
                    <td class="ps-4 py-3"><span class="text-muted small font-weight-bold">${x(l.id)}</span></td>
                    <td class="py-3">
                        <div class="font-weight-bold text-dark">${x(l.employee)}</div>
                        <div class="text-muted small font-weight-bold text-uppercase" style="font-size:0.7rem">${x(l.employee_id)}</div>
                    </td>
                    <td class="py-3">${typeBadge}</td>
                    <td class="text-end font-weight-bold text-secondary py-3">\u20b1${x(l.amount)}</td>
                    <td class="text-end text-muted small font-weight-bold py-3">\u20b1${x(l.monthly_amortization)}</td>
                    <td class="py-3">${progress}</td>
                    <td class="text-end py-3">${balanceCell}</td>
                    <td class="text-center py-3">${statusBadge}</td>
                    <td class="text-center pe-4 py-3 text-nowrap">${actions}</td>
                </tr>`;
        }).join('');
    }

    function updateTabCounts(data) {
        const active    = data.filter(l => l.status === 'active').length;
        const completed = data.filter(l => l.status === 'completed').length;
        setText('tabActive',    active);
        setText('tabCompleted', completed);
        setText('tabAll',       data.length);
    }

    // ─── TABS ─────────────────────────────────────────────────────────────────
    window.setTab = function (el) {
        document.querySelectorAll('#loanTabs .nav-link').forEach(a => {
            a.classList.remove('active', 'text-secondary');
            a.classList.add('text-muted');
        });
        el.classList.add('active', 'text-secondary');
        el.classList.remove('text-muted');
        currentStatus = el.getAttribute('data-status');
        loadList();
    };

    // ─── DEBOUNCE SEARCH ─────────────────────────────────────────────────────
    window.debounceLoad = function () {
        clearTimeout(empSearchTimer);
        empSearchTimer = setTimeout(loadList, 350);
    };

    // ─── EMPLOYEE SEARCH (add modal) ─────────────────────────────────────────
    window.searchEmployees = function () {
        const input    = document.getElementById('addEmpSearch');
        const dropdown = document.getElementById('addEmpDropdown');
        const val      = input.value.trim();

        document.getElementById('addEmpId').value = '';
        document.getElementById('addEmpSelected').classList.add('d-none');

        if (!val) { dropdown.classList.add('d-none'); return; }

        fetch(BASE + '/employees?q=' + encodeURIComponent(val))
            .then(r => r.json())
            .then(function (results) {
                if (!results.length) {
                    dropdown.innerHTML = `<div class="list-group-item text-muted small py-3 font-weight-bold text-center">No results found.</div>`;
                } else {
                    dropdown.innerHTML = results.map(function (e) {
                        return `<button type="button" class="list-group-item list-group-item-action py-2" onclick="selectEmployee('${x(e.id)}', '${x(e.full_name)}')">
                            <strong class="text-dark">${x(e.full_name)}</strong> 
                            <span class="text-muted small mx-1">(${x(e.id)})</span> 
                            <span class="badge bg-light border text-dark float-end">${x(e.department)}</span>
                        </button>`;
                    }).join('');
                }
                dropdown.classList.remove('d-none');
            })
            .catch(console.error);
    };

    window.selectEmployee = function (id, name) {
        document.getElementById('addEmpSearch').value         = name;
        document.getElementById('addEmpId').value             = id;
        document.getElementById('addEmpDropdown').classList.add('d-none');
        document.getElementById('addEmpSelectedName').textContent = `${name} (${id})`;
        document.getElementById('addEmpSelected').classList.remove('d-none');
    };

    // ─── AUTO AMORTIZATION ───────────────────────────────────────────────────
    window.autoCalcAmortization = function () {
        const amount = parseFloat(document.getElementById('addAmount').value) || 0;
        const term   = parseInt(document.getElementById('addTerm').value)     || 0;
        const sumEl  = document.getElementById('addSummary');

        if (amount > 0 && term > 0) {
            const monthly = (amount / term).toFixed(2);
            document.getElementById('addAmortization').value = monthly;
            setText('sumAmount',  '\u20b1' + fmt(amount));
            setText('sumMonthly', '\u20b1' + fmt(monthly));
            setText('sumTerm',    term + ' months');
            setText('sumTotal',   '\u20b1' + fmt((monthly * term).toFixed(2)));
            sumEl.classList.remove('d-none');
        } else {
            sumEl.classList.add('d-none');
        }
    };

    // ─── ADD LOAN ─────────────────────────────────────────────────────────────
    window.openAddModal = function () {
        ['addEmpSearch', 'addAmount', 'addAmortization', 'addStartDate', 'addEmpId', 'addNotes']
            .forEach(id => { document.getElementById(id).value = ''; });
        document.getElementById('addLoanType').value = '';
        document.getElementById('addTerm').value     = '24';
        document.getElementById('addEmpDropdown').classList.add('d-none');
        document.getElementById('addEmpSelected').classList.add('d-none');
        document.getElementById('addSummary').classList.add('d-none');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addModal')).show();
    };

    window.submitAddLoan = function () {
        const empId = document.getElementById('addEmpId').value;
        const type  = document.getElementById('addLoanType').value;
        const amt   = document.getElementById('addAmount').value;
        const amor  = document.getElementById('addAmortization').value;
        const date  = document.getElementById('addStartDate').value;
        const term  = document.getElementById('addTerm').value;
        const notes = document.getElementById('addNotes').value;

        if (!empId || !type || !amt || !amor || !date || !term) {
            Swal.fire({ icon: 'warning', title: 'Incomplete', text: 'Please fill in all required fields.', confirmButtonColor: '#1a1a1a' });
            return;
        }

        const btn = document.getElementById('addSubmitBtn');
        btn.disabled = true;
        btn.textContent = 'Saving…';

        fetch(BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                user_id:               empId,
                loan_type:             type,
                amount:                amt,
                monthly_amortization:  amor,
                term_months:           term,
                start_date:            date,
                notes:                 notes,
            }),
        })
        .then(handleJsonResponse)
        .then(function (res) {
            bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
            toast(res.message);
            loadStats();
            loadList();
        })
        .catch(handleError)
        .finally(function () {
            btn.disabled    = false;
            btn.textContent = 'Add Loan';
        });
    };

    // ─── EDIT LOAN ────────────────────────────────────────────────────────────
    window.openEditModal = function (id) {
        fetch(BASE + '/' + id, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(function (l) {
                editPayments = l.payments_made;
                document.getElementById('editLoanId').value             = l.id;
                document.getElementById('editEmpName').textContent      = l.employee;
                document.getElementById('editLoanTypeName').textContent = l.loan_type_name;
                document.getElementById('editPaymentsMade').textContent = l.payments_made + '/' + l.term_months;

                document.getElementById('editAmount').value        = l.amount.replace(/,/g, '');
                document.getElementById('editAmortization').value  = l.monthly_amortization.replace(/,/g, '');
                document.getElementById('editStartDate').value     = rawDate(l.start_date);
                document.getElementById('editTerm').value          = l.term_months;
                document.getElementById('editNotes').value         = l.notes || '';
                document.getElementById('editSummary').classList.add('d-none');

                bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
            })
            .catch(handleError);
    };

    window.updateEditSummary = function () {
        const amount = parseFloat(document.getElementById('editAmount').value)       || 0;
        const amor   = parseFloat(document.getElementById('editAmortization').value) || 0;
        const sumEl  = document.getElementById('editSummary');

        if (!amount || !amor) { sumEl.classList.add('d-none'); return; }

        const totalPaid = amor * editPayments;
        const balance   = Math.max(0, amount - totalPaid);
        setText('editSumPaid',      editPayments);
        setText('editSumTotalPaid', '\u20b1' + fmt(totalPaid));
        setText('editSumBalance',   '\u20b1' + fmt(balance));
        sumEl.classList.remove('d-none');
    };

    window.submitEditLoan = function () {
        const id   = document.getElementById('editLoanId').value;
        const amt  = document.getElementById('editAmount').value;
        const amor = document.getElementById('editAmortization').value;
        const date = document.getElementById('editStartDate').value;
        const term = document.getElementById('editTerm').value;
        const notes= document.getElementById('editNotes').value;

        if (!amt || !amor || !date || !term) {
            Swal.fire({ icon: 'warning', title: 'Incomplete', text: 'Please fill in all required fields.', confirmButtonColor: '#1a1a1a' });
            return;
        }

        fetch(BASE + '/' + id, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                amount: amt, monthly_amortization: amor,
                term_months: term, start_date: date, notes: notes,
            }),
        })
        .then(handleJsonResponse)
        .then(function (res) {
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            toast(res.message);
            loadStats();
            loadList();
        })
        .catch(handleError);
    };

    // ─── VIEW LOAN ────────────────────────────────────────────────────────────
    window.openViewModal = function (id) {
        const body = document.getElementById('viewModalBody');
        body.innerHTML = `<div class="text-center py-5"><span class="spinner-border spinner-border-sm text-secondary"></span></div>`;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModal')).show();

        fetch(BASE + '/' + id, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(function (l) {
                const typeBadge = `<span class="badge ${l.loan_type === 'sss' ? 'bg-secondary text-white' : 'bg-light border text-dark'} px-2 py-1 text-uppercase">${x(l.loan_type_name)}</span>`;
                const statusBadge = l.status === 'active'
                    ? `<span class="badge bg-secondary px-2 py-1 text-uppercase">Active</span>`
                    : `<span class="badge bg-light border text-muted px-2 py-1 text-uppercase">Completed</span>`;

                const nextSection = l.status === 'active' && l.next_payment_date ? `
                    <div class="border rounded p-3 bg-white shadow-sm mb-4">
                        <div class="row g-3">
                            <div class="col-md-6 border-end">
                                <span class="text-muted small font-weight-bold text-uppercase">Next Payment Date</span><br>
                                <span class="font-weight-bold text-dark">${x(l.next_payment_date)}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small font-weight-bold text-uppercase">Next Payment Amount</span><br>
                                <span class="font-weight-bold text-dark">&#8369;${x(l.monthly_amortization)}</span>
                            </div>
                        </div>
                    </div>` : '';

                const recentPayments = l.recent_payments && l.recent_payments.length
                    ? `<div class="mt-4"><p class="text-muted small font-weight-bold text-uppercase border-bottom pb-2 mb-3">Recent Payments</p>
                       <table class="table table-hover align-middle mb-0 small border rounded shadow-sm overflow-hidden">
                           <thead class="bg-light text-muted text-uppercase"><tr>
                               <th class="border-0 ps-3 py-2">Date</th>
                               <th class="border-0 py-2 text-end">Amount</th>
                               <th class="border-0 py-2 text-end">Balance After</th>
                               <th class="border-0 pe-3 py-2">Type</th>
                           </tr></thead><tbody>` +
                       l.recent_payments.map(p => `
                           <tr class="bg-white border-bottom">
                               <td class="ps-3 font-weight-bold text-dark">${x(p.date)}</td>
                               <td class="text-end font-weight-bold text-secondary">\u20b1${x(p.amount)}</td>
                               <td class="text-end font-weight-bold text-dark">\u20b1${x(p.balance_after)}</td>
                               <td class="pe-3"><span class="badge bg-light border text-muted text-uppercase">${x(p.type)}</span></td>
                           </tr>`).join('') +
                       `</tbody></table></div>`
                    : `<p class="text-muted small font-weight-bold text-uppercase mt-4 mb-0 text-center py-4 bg-white border rounded">No payment records yet.</p>`;

                body.innerHTML = `
                    <div class="row g-3 text-center mb-4 pb-3 border-bottom">
                        <div class="col-3"><div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Loan ID</div><span class="text-dark font-weight-bold small">${x(l.id)}</span></div>
                        <div class="col-3"><div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Status</div>${statusBadge}</div>
                        <div class="col-3"><div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Start Date</div><div class="small font-weight-bold text-dark">${x(l.start_date)}</div></div>
                        <div class="col-3"><div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Created</div><div class="small text-muted font-weight-bold">${x(l.created_at)}</div></div>
                    </div>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1">Employee</div>
                            <div class="font-weight-bold text-dark h6 mb-0">${x(l.employee)}</div>
                            <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">${x(l.employee_id)}</small>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1">Loan Type</div>
                            ${typeBadge}
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between text-muted small font-weight-bold text-uppercase mb-2">
                            <span>Payment Progress</span>
                            <span>${l.payments_made}/${l.term_months} (${l.progress_percent}%)</span>
                        </div>
                        <div class="progress bg-white border shadow-sm" style="height:10px">
                            <div class="progress-bar bg-secondary" style="width:${l.progress_percent}%"></div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-white shadow-sm text-center">
                                <div class="text-muted small font-weight-bold text-uppercase mb-2">Remaining Balance</div>
                                <div class="h4 font-weight-bold text-dark mb-0">&#8369;${x(l.remaining_balance)}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-white shadow-sm text-center">
                                <div class="text-muted small font-weight-bold text-uppercase mb-2">Loan Amount</div>
                                <div class="font-weight-bold text-dark mb-1 h5">&#8369;${x(l.amount)}</div>
                                <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">Monthly: &#8369;${x(l.monthly_amortization)}</small>
                            </div>
                        </div>
                    </div>
                    
                    ${nextSection}
                    ${recentPayments}
                    
                    <p class="text-muted small mt-4 pt-3 border-top mb-0 font-weight-bold text-uppercase text-end" style="font-size: 0.65rem;">Encoded by: <span class="text-dark">${x(l.encoded_by)}</span></p>`;
            })
            .catch(handleError);
    };

    // ─── DELETE LOAN ─────────────────────────────────────────────────────────
    window.openDeleteModal = function (id, employee, loanTypeName, balance) {
        deleteLoanId = id;
        document.getElementById('deleteInfo').innerHTML = `
            <div class="mb-2"><span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">Employee:</span> <strong class="text-dark ms-1 d-block">${x(employee)}</strong></div>
            <div class="mb-2"><span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">Type:</span> <span class="text-dark ms-1 font-weight-bold d-block">${x(loanTypeName)}</span></div>
            <div><span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">Balance:</span> <strong class="text-dark ms-1 h6 d-block mb-0">\u20b1${x(balance)}</strong></div>`;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal')).show();
    };

    window.confirmDelete = function () {
        const btn = document.getElementById('confirmDeleteBtn');
        btn.disabled = true;

        fetch(BASE + '/' + deleteLoanId, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        })
        .then(handleJsonResponse)
        .then(function (res) {
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            toast(res.message);
            loadStats();
            loadList();
        })
        .catch(handleError)
        .finally(function () {
            btn.disabled = false;
        });
    };

    // ─── UTILITIES ────────────────────────────────────────────────────────────
    function handleJsonResponse(r) {
        return r.json().then(function (data) {
            if (!r.ok) return Promise.reject(data);
            return data;
        });
    }

    function handleError(err) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: err && err.message ? err.message : 'Something went wrong.',
            confirmButtonColor: '#1a1a1a'
        });
    }

    function toast(msg) {
        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: msg, showConfirmButton: false,
            timer: 2500, timerProgressBar: true,
        });
    }

    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val !== null && val !== undefined ? val : '—';
    }

    function fmt(n) {
        return Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function rawDate(str) {
        if (!str) return '';
        const d = new Date(str);
        if (isNaN(d)) return '';
        return d.toISOString().split('T')[0];
    }

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