@extends('layouts.main')

@section('title', 'Employee Salary Management')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">Salary Management</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Salary Management</h4>
        <small class="text-muted font-weight-bold text-uppercase">Manage and update employee compensation</small>
    </div>
    <button class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm" id="btn-bulk-toggle">
        Bulk Update
    </button>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="border rounded bg-white p-4 shadow-sm h-100 d-flex align-items-center gap-4">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 border border-secondary" style="width: 50px; height: 50px;">
                <i class="bi bi-people-fill fs-5 text-dark"></i>
            </div>
            <div>
                <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Total Employees</span>
                <span class="h4 font-weight-bold text-dark mb-0" id="stat-total">
                    <span class="spinner-border spinner-border-sm text-secondary"></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="border rounded bg-white p-4 shadow-sm h-100 d-flex align-items-center gap-4">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 border border-secondary" style="width: 50px; height: 50px;">
                <i class="bi bi-cash-stack fs-5 text-dark"></i>
            </div>
            <div>
                <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Total Monthly Payroll</span>
                <span class="h4 font-weight-bold text-dark mb-0" id="stat-payroll">
                    <span class="spinner-border spinner-border-sm text-secondary"></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="border rounded bg-white p-4 shadow-sm h-100 d-flex align-items-center gap-4">
            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 border border-secondary" style="width: 50px; height: 50px;">
                <i class="bi bi-bar-chart-fill fs-5 text-dark"></i>
            </div>
            <div>
                <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Average Salary</span>
                <span class="h4 font-weight-bold text-dark mb-0" id="stat-avg">
                    <span class="spinner-border spinner-border-sm text-secondary"></span>
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Main Card --}}
<div class="card shadow-sm border-0 mb-5">
    
    {{-- Bulk Panel --}}
    <div class="card-body bg-white border-bottom p-4 d-none" id="bulk-panel">
        <h6 class="font-weight-bold text-dark text-uppercase mb-3">Bulk Salary Update</h6>
        <div class="row g-3 align-items-end mb-4">
            <div class="col-md-4">
                <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Filter by Position</label>
                <select class="form-select shadow-sm font-weight-bold text-dark border-secondary" id="bulk-position">
                    <option value="">— Select Position —</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos }}">{{ $pos }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">New Monthly Salary</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-light border-secondary text-dark font-weight-bold">₱</span>
                    <input type="number" class="form-control border-secondary font-weight-bold text-dark" id="bulk-salary" placeholder="0.00" min="0" step="0.01">
                </div>
            </div>
            <div class="col-md-4 d-flex gap-3">
                <button class="btn btn-secondary font-weight-bold px-4 shadow-sm w-50" id="btn-bulk-apply">Apply</button>
                <button class="btn btn-outline-dark font-weight-bold px-4 w-50" id="btn-bulk-cancel">Cancel</button>
            </div>
        </div>

        <div id="bulk-employee-list" class="d-none">
            <div class="d-flex align-items-center justify-content-between mb-3 border-top pt-3">
                <span class="badge bg-light border text-dark px-3 py-2 text-uppercase font-weight-bold" id="bulk-count-label">0 found</span>
                <div class="form-check mb-0">
                    <input class="form-check-input border-secondary" type="checkbox" id="bulk-select-all" style="cursor:pointer;">
                    <label class="form-check-label small font-weight-bold text-uppercase text-dark ms-1" for="bulk-select-all" style="cursor:pointer;">Select All</label>
                </div>
            </div>
            <div class="row g-3" id="bulk-employees"></div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card-body bg-light border-bottom p-4">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 font-weight-bold text-dark ps-0" id="filter-search" placeholder="Search name, ID, position…">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select shadow-sm font-weight-bold text-dark" id="filter-department">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select shadow-sm font-weight-bold text-dark" id="filter-position">
                    <option value="">All Positions</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos }}">{{ $pos }}</option>
                    @endforeach
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
                        <th class="border-0 ps-4 py-3" style="width:60px"></th>
                        <th class="border-0 py-3">Employee</th>
                        <th class="border-0 py-3">Department / Position</th>
                        <th class="border-0 py-3 text-end">Monthly Salary</th>
                        <th class="border-0 py-3 text-end">Daily Rate</th>
                        <th class="border-0 py-3 text-end">Hourly Rate</th>
                        <th class="border-0 py-3 pe-4 text-center" style="width:120px">Actions</th>
                    </tr>
                </thead>
                <tbody id="salary-tbody">
                    <tr>
                        <td colspan="7" class="text-center py-5 bg-white text-muted font-weight-bold">
                            <span class="spinner-border spinner-border-sm me-2 text-secondary"></span>Loading Employees…
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer bg-light py-3 border-top">
        <small class="text-muted font-weight-bold text-uppercase" id="table-footer">—</small>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="edit-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase">Edit Salary</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="edit-employee-id">
                
                <div class="bg-white border rounded p-4 shadow-sm mb-4">
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Employee</label>
                    <input type="text" class="form-control bg-light font-weight-bold text-dark mb-3" id="edit-employee-name" readonly>
                    
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                        Monthly Basic Salary <span class="text-danger">*</span>
                    </label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-light text-dark font-weight-bold border-secondary">₱</span>
                        <input type="number" class="form-control border-secondary font-weight-bold text-dark" id="edit-basic-salary" min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>

                <div class="border border-secondary rounded bg-white p-3 shadow-sm text-center">
                    <p class="text-dark small font-weight-bold text-uppercase border-bottom pb-2 mb-3">Computed Rates (Preview)</p>
                    <div class="row g-3">
                        <div class="col-6 border-end">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Daily Rate</div>
                            <span class="font-weight-bold text-dark" id="edit-daily-preview">—</span>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size: 0.65rem;">Hourly Rate</div>
                            <span class="font-weight-bold text-dark" id="edit-hourly-preview">—</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white py-3">
                <button class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-secondary font-weight-bold px-4 shadow-sm" id="btn-save-edit">Save Changes</button>
            </div>
        </div>
    </div>
</div>

{{-- Details Modal --}}
<div class="modal fade" id="details-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase" id="details-modal-title">Employee Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light" id="details-body">
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

@endsection

@push('scripts')
<script>
const SalaryManager = (() => {

    /* ─── Config ────────────────────────────────────────────── */
    const ROUTES = {
        list:        '{{ route('accounting.salary.list') }}',
        show:        '{{ url('accounting/salary') }}',
        update:      '{{ url('accounting/salary') }}',
        bulkUpdate:  '{{ route('accounting.salary.bulk-update') }}',
    };

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    /* ─── Helpers ───────────────────────────────────────────── */
    const peso = n => '₱' + parseFloat(n || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    async function apiFetch(url, options = {}) {
        const res = await fetch(url, {
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            ...options,
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message ?? `HTTP ${res.status}`);
        }
        return res.json();
    }

    function x(str) {
        if (str == null) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    /* ─── State ─────────────────────────────────────────────── */
    let allEmployees = [];
    let filtered     = [];
    let bulkChecked  = new Set();

    /* ─── DOM refs ──────────────────────────────────────────── */
    const $ = id => document.getElementById(id);
    const tbody         = $('salary-tbody');
    const filterSearch  = $('filter-search');
    const filterDept    = $('filter-department');
    const filterPos     = $('filter-position');
    const bulkPanel     = $('bulk-panel');
    const bulkPosSel    = $('bulk-position');
    const bulkSalaryInp = $('bulk-salary');
    const bulkEmpList   = $('bulk-employee-list');
    const bulkEmps      = $('bulk-employees');
    const bulkSelectAll = $('bulk-select-all');

    /* ─── Load employees from server ────────────────────────── */
    async function loadEmployees() {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 bg-white text-muted font-weight-bold">
            <span class="spinner-border spinner-border-sm me-2 text-secondary"></span>Loading Employees…</td></tr>`;

        const params = new URLSearchParams({ search: filterSearch.value, department: filterDept.value, position: filterPos.value });

        try {
            allEmployees = await apiFetch(`${ROUTES.list}?${params}`);
            filtered     = [...allEmployees];
            updateStats();
            renderTable();
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 bg-white text-dark font-weight-bold">Error: ${e.message}</td></tr>`;
        }
    }

    /* ─── Stats ─────────────────────────────────────────────── */
    function updateStats() {
        const total   = allEmployees.length;
        const payroll = allEmployees.reduce((s, e) => s + parseFloat(e.basicSalary || 0), 0);
        $('stat-total').textContent   = total;
        $('stat-payroll').textContent = peso(payroll);
        $('stat-avg').textContent     = peso(total ? payroll / total : 0);
    }

    /* ─── Render Table ──────────────────────────────────────── */
    function renderTable() {
        if (!filtered.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 bg-white text-muted font-weight-bold">No employees found.</td></tr>`;
            $('table-footer').textContent = 'No results';
            return;
        }

        tbody.innerHTML = filtered.map(emp => {
            const initials = emp.fullName.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
            return `
            <tr class="border-bottom bg-white">
                <td class="text-center ps-4 py-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white font-weight-bold shadow-sm" style="width:36px;height:36px;font-size:.8rem">
                        ${initials}
                    </span>
                </td>
                <td class="py-3">
                    <div class="font-weight-bold text-dark">${x(emp.fullName)}</div>
                    <div class="text-muted small font-weight-bold text-uppercase" style="font-size:0.7rem">
                        ${x(emp.id)} ${emp.email ? `<span class="mx-1">|</span> <span class="text-secondary">${x(emp.email)}</span>` : ''}
                    </div>
                </td>
                <td class="py-3">
                    <div class="font-weight-bold text-dark">${x(emp.department) || '—'}</div>
                    <div class="text-muted small font-weight-bold text-uppercase" style="font-size:0.7rem">${x(emp.position) || '—'}</div>
                </td>
                <td class="text-end font-weight-bold text-secondary py-3">${peso(emp.basicSalary)}</td>
                <td class="text-end text-muted font-weight-bold py-3">${peso(emp.dailyRate)}</td>
                <td class="text-end text-muted font-weight-bold py-3">${peso(emp.hourlyRate)}</td>
                <td class="text-center pe-4 py-3">
                    <button class="btn btn-sm btn-light border text-dark font-weight-bold px-2 me-1 shadow-sm btn-view" data-id="${emp.id}" title="View Details">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-dark font-weight-bold px-2 btn-edit" 
                            data-id="${emp.id}" data-name="${x(emp.fullName)}" data-salary="${emp.basicSalary}" title="Edit Salary">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                </td>
            </tr>`;
        }).join('');

        $('table-footer').textContent = `Showing ${filtered.length} of ${allEmployees.length} employee(s)`;
    }

    /* ─── Client-side filter ────────────────────────────────── */
    function applyFilters() {
        const q    = filterSearch.value.toLowerCase();
        const dept = filterDept.value;
        const pos  = filterPos.value;

        filtered = allEmployees.filter(e => {
            const matchQ    = !q || [e.fullName, e.id, e.position, e.department].some(v => (v ?? '').toLowerCase().includes(q));
            const matchDept = !dept || e.department === dept;
            const matchPos  = !pos  || e.position   === pos;
            return matchQ && matchDept && matchPos;
        });

        renderTable();
    }

    /* ─── Edit Modal ────────────────────────────────────────── */
    function openEdit(btn) {
        $('edit-employee-id').value   = btn.dataset.id;
        $('edit-employee-name').value = btn.dataset.name;
        $('edit-basic-salary').value  = btn.dataset.salary;
        refreshEditPreview(btn.dataset.salary);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('edit-modal')).show();
    }

    function refreshEditPreview(val) {
        const n  = parseFloat(val);
        const ok = !isNaN(n) && n > 0;
        $('edit-daily-preview').textContent  = ok ? peso(n / 26)      : '—';
        $('edit-hourly-preview').textContent = ok ? peso(n / 26 / 8)  : '—';
    }

    async function saveEdit() {
        const id  = $('edit-employee-id').value;
        const val = parseFloat($('edit-basic-salary').value);
        const btn = $('btn-save-edit');

        if (!val || val <= 0) {
            Swal.fire({ icon: 'warning', title: 'Invalid Amount', text: 'Enter a valid salary amount.', confirmButtonColor: '#1a1a1a' });
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

        try {
            await apiFetch(`${ROUTES.update}/${id}`, {
                method: 'PATCH',
                body:   JSON.stringify({ basicSalary: val }),
            });

            Swal.fire({ icon: 'success', title: 'Salary Updated', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
            bootstrap.Modal.getInstance(document.getElementById('edit-modal')).hide();
            await loadEmployees();

        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#1a1a1a' });
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Save Changes';
        }
    }

    /* ─── Details Modal ─────────────────────────────────────── */
    async function openDetails(id) {
        $('details-modal-title').textContent = 'Loading…';
        $('details-body').innerHTML = `<div class="text-center py-5"><span class="spinner-border spinner-border-sm text-secondary"></span></div>`;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('details-modal')).show();

        try {
            const { employee: emp, contributions: gc } = await apiFetch(`${ROUTES.show}/${id}`);
            $('details-modal-title').textContent = emp.fullName;
            
            const badgeCls = (emp.employmentStatus ?? '').toLowerCase() === 'regular' ? 'bg-secondary text-white' : 'bg-light border text-muted';

            $('details-body').innerHTML = `
                <div class="bg-white border rounded p-4 shadow-sm mb-4">
                    <div class="row g-4">
                        <div class="col-md-4 border-end">
                            <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Employee ID</span>
                            <span class="font-weight-bold text-dark">${x(emp.id)}</span>
                        </div>
                        <div class="col-md-4 border-end">
                            <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Department / Position</span>
                            <span class="font-weight-bold text-dark d-block lh-sm">${x(emp.department) ?? '—'}<br><span class="text-secondary small">${x(emp.position) ?? '—'}</span></span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Status / Hire Date</span>
                            <span class="badge ${badgeCls} px-2 py-1 text-uppercase mb-1 d-inline-block">${x(emp.employmentStatus) ?? '—'}</span>
                            <div class="text-dark font-weight-bold small">${x(emp.hireDate) ?? '—'}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="border rounded bg-white p-3 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
                            <span class="text-muted small font-weight-bold text-uppercase mb-2">Monthly Basic</span>
                            <span class="h5 font-weight-bold text-dark mb-0">${peso(emp.basicSalary)}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded bg-white p-3 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
                            <span class="text-muted small font-weight-bold text-uppercase mb-2">Daily Rate</span>
                            <span class="h5 font-weight-bold text-secondary mb-0">${peso(emp.dailyRate)}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded bg-white p-3 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
                            <span class="text-muted small font-weight-bold text-uppercase mb-2">Hourly Rate</span>
                            <span class="h5 font-weight-bold text-secondary mb-0">${peso(emp.hourlyRate)}</span>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="text-muted small font-weight-bold text-uppercase mb-0">Est. Monthly Contributions</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="border-0 ps-4 py-3">Type</th>
                                    <th class="border-0 text-end py-3">EE Share</th>
                                    <th class="border-0 text-end pe-4 py-3">ER Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-bottom bg-white">
                                    <td class="ps-4 py-3 font-weight-bold text-dark">SSS</td>
                                    <td class="text-end font-weight-bold text-danger py-3">${peso(gc.sss * 2)}</td>
                                    <td class="text-end text-muted font-weight-bold pe-4 py-3">—</td>
                                </tr>
                                <tr class="border-bottom bg-white">
                                    <td class="ps-4 py-3 font-weight-bold text-dark">PhilHealth</td>
                                    <td class="text-end font-weight-bold text-danger py-3">${peso(gc.philhealth * 2)}</td>
                                    <td class="text-end text-muted font-weight-bold pe-4 py-3">—</td>
                                </tr>
                                <tr class="border-bottom bg-white">
                                    <td class="ps-4 py-3 font-weight-bold text-dark">Pag-IBIG</td>
                                    <td class="text-end font-weight-bold text-danger py-3">${peso(gc.pagibig * 2)}</td>
                                    <td class="text-end text-muted font-weight-bold pe-4 py-3">—</td>
                                </tr>
                                <tr class="border-bottom bg-white">
                                    <td class="ps-4 py-3 font-weight-bold text-dark">Withholding Tax</td>
                                    <td class="text-end font-weight-bold text-danger py-3">${peso(gc.tax * 12)}</td>
                                    <td class="text-end text-muted font-weight-bold pe-4 py-3">—</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td class="font-weight-bold text-dark text-uppercase ps-4 py-3">Est. Net Pay</td>
                                    <td class="text-end font-weight-bold text-success py-3">${peso(parseFloat(emp.basicSalary) - (gc.sss * 2) - (gc.philhealth * 2) - (gc.pagibig * 2) - (gc.tax * 12))}</td>
                                    <td class="pe-4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>`;

        } catch (e) {
            $('details-body').innerHTML = `<div class="text-dark font-weight-bold text-center py-5">Error: ${e.message}</div>`;
        }
    }

    /* ─── Bulk Update ───────────────────────────────────────── */
    function renderBulkList() {
        const pos = bulkPosSel.value;
        if (!pos) { bulkEmpList.classList.add('d-none'); return; }

        const list = allEmployees.filter(e => e.position === pos);
        $('bulk-count-label').textContent = `${list.length} employee(s) in this position`;
        bulkChecked.clear();
        bulkSelectAll.checked = false;

        bulkEmps.innerHTML = list.map(emp => `
            <div class="col-md-6">
                <div class="border rounded p-3 bg-light">
                    <div class="form-check mb-0">
                        <input class="form-check-input bulk-chk border-secondary" type="checkbox" value="${emp.id}" id="bc-${emp.id}" style="cursor:pointer;">
                        <label class="form-check-label w-100 ms-1" for="bc-${emp.id}" style="cursor:pointer">
                            <div class="font-weight-bold text-dark">${x(emp.fullName)}</div>
                            <div class="text-muted small font-weight-bold text-uppercase mb-2" style="font-size:0.65rem;">${x(emp.id)} <span class="mx-1">|</span> ${x(emp.department) ?? ''}</div>
                            <div class="badge bg-white border text-dark font-weight-bold py-1 w-100 text-start">Current: ${peso(emp.basicSalary)}</div>
                        </label>
                    </div>
                </div>
            </div>`).join('');

        bulkEmpList.classList.remove('d-none');

        bulkEmps.querySelectorAll('.bulk-chk').forEach(chk => {
            chk.addEventListener('change', () => chk.checked ? bulkChecked.add(chk.value) : bulkChecked.delete(chk.value));
        });
    }

    async function applyBulk() {
        const newSalary = parseFloat(bulkSalaryInp.value);

        if (!newSalary || newSalary <= 0) {
            Swal.fire({ icon: 'warning', title: 'Missing Salary', text: 'Enter a valid salary amount.', confirmButtonColor: '#1a1a1a' });
            return;
        }
        if (!bulkChecked.size) {
            Swal.fire({ icon: 'warning', title: 'No Selection', text: 'Select at least one employee.', confirmButtonColor: '#1a1a1a' });
            return;
        }

        const confirm = await Swal.fire({
            title: 'Confirm Bulk Update',
            html: `Update <strong>${bulkChecked.size}</strong> employee(s) to <strong>${peso(newSalary)}</strong>?`,
            icon: 'question', showCancelButton: true,
            confirmButtonColor: '#1a1a1a', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Update',
        });

        if (!confirm.isConfirmed) return;

        const btn = $('btn-bulk-apply');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

        try {
            const data = await apiFetch(ROUTES.bulkUpdate, {
                method: 'POST',
                body:   JSON.stringify({ user_ids: [...bulkChecked], basicSalary: newSalary }),
            });

            Swal.fire({ icon: 'success', title: 'Done!', text: data.message, timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
            closeBulk();
            await loadEmployees();
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#1a1a1a' });
        } finally {
            btn.disabled = false;
            btn.textContent = 'Apply';
        }
    }

    function closeBulk() {
        bulkPanel.classList.add('d-none');
        bulkPosSel.value    = '';
        bulkSalaryInp.value = '';
        bulkChecked.clear();
        bulkEmpList.classList.add('d-none');
        $('btn-bulk-toggle').innerHTML = '<i class="bi bi-lightning-fill me-2"></i>Bulk Update';
        $('btn-bulk-toggle').classList.replace('btn-outline-dark', 'btn-secondary');
    }

    /* ─── Event Binding ─────────────────────────────────────── */
    function bindEvents() {
        let searchTimer;
        filterSearch.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilters, 300);
        });
        filterDept.addEventListener('change', applyFilters);
        filterPos.addEventListener('change',  applyFilters);

        $('btn-bulk-toggle').addEventListener('click', () => {
            const isOpen = !bulkPanel.classList.toggle('d-none');
            if(isOpen) {
                $('btn-bulk-toggle').innerHTML = '<i class="bi bi-x me-2"></i>Close Bulk';
                $('btn-bulk-toggle').classList.replace('btn-secondary', 'btn-outline-dark');
            } else {
                closeBulk();
            }
        });

        $('btn-bulk-cancel').addEventListener('click', closeBulk);
        $('btn-bulk-apply').addEventListener('click',  applyBulk);
        bulkPosSel.addEventListener('change', renderBulkList);

        bulkSelectAll.addEventListener('change', () => {
            bulkEmps.querySelectorAll('.bulk-chk').forEach(chk => {
                chk.checked = bulkSelectAll.checked;
                bulkSelectAll.checked ? bulkChecked.add(chk.value) : bulkChecked.delete(chk.value);
            });
        });

        $('edit-basic-salary').addEventListener('input', e => refreshEditPreview(e.target.value));
        $('btn-save-edit').addEventListener('click', saveEdit);

        tbody.addEventListener('click', e => {
            const editBtn = e.target.closest('.btn-edit');
            const viewBtn = e.target.closest('.btn-view');
            if (editBtn) openEdit(editBtn);
            if (viewBtn) openDetails(viewBtn.dataset.id);
        });
    }

    /* ─── Init ──────────────────────────────────────────────── */
    async function init() {
        bindEvents();
        await loadEmployees();
    }

    return { init };
})();

document.addEventListener('DOMContentLoaded', () => SalaryManager.init());
</script>
@endpush