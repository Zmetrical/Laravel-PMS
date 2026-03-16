@extends('layouts.main')

@section('title', 'Department Management')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="#" class="text-secondary text-decoration-none">Organization</a></li>
        <li class="breadcrumb-item active text-muted">Departments</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Department Management</h4>
        <small class="text-muted font-weight-bold text-uppercase">Organize and manage company departments</small>
    </div>
    <button class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm" onclick="openAddModal()">
        Add Department
    </button>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-5">
    <div class="col-sm-6 col-xl-3">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Total Departments</span>
            <span class="h2 font-weight-bold text-dark mb-1" id="stat-total">
                <span class="spinner-border spinner-border-sm text-secondary"></span>
            </span>
            <span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;" id="stat-active-sub">— active</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Total Employees</span>
            <span class="h2 font-weight-bold text-dark mb-1" id="stat-employees">
                <span class="spinner-border spinner-border-sm text-secondary"></span>
            </span>
            <span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;">Across all departments</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Largest Department</span>
            <span class="h5 font-weight-bold text-dark mb-1" id="stat-largest-name">—</span>
            <span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;" id="stat-largest-count">—</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="border border-secondary rounded bg-light p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-dark small font-weight-bold text-uppercase mb-2">Branches</span>
            <span class="h2 font-weight-bold text-dark mb-1" id="stat-branches">—</span>
            <span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;" id="stat-branches-sub">—</span>
        </div>
    </div>
</div>

{{-- Filter + Table --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Department Directory</h6>
    </div>

    <div class="card-body bg-light border-bottom p-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="filter-search" class="form-control border-start-0 font-weight-bold text-dark ps-0" placeholder="Search name, code…" oninput="debounceList()">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filter-branch" class="form-select shadow-sm font-weight-bold text-dark" onchange="loadList()">
                    <option value="">All Branches</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="filter-status" class="form-select shadow-sm font-weight-bold text-dark" onchange="loadList()">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-1">
                <select id="filter-per-page" class="form-select shadow-sm font-weight-bold text-dark" onchange="onPerPageChange()">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-md-2 text-md-end">
                <button class="btn btn-outline-dark font-weight-bold shadow-sm w-100" onclick="resetFilters()">
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 ps-4 py-3" style="width:25%">Department</th>
                        <th class="border-0 py-3" style="width:10%">Code</th>
                        <th class="border-0 py-3" style="width:20%">Head(s)</th>
                        <th class="border-0 py-3 text-center" style="width:10%">Employees</th>
                        <th class="border-0 py-3" style="width:15%">Branch</th>
                        <th class="border-0 py-3 text-center" style="width:10%">Status</th>
                        <th class="border-0 text-center pe-4 py-3" style="width:10%">Actions</th>
                    </tr>
                </thead>
                <tbody id="dept-tbody">
                    <tr>
                        <td colspan="7" class="text-center py-5 bg-white text-muted font-weight-bold text-uppercase">
                            <span class="spinner-border spinner-border-sm me-2 text-secondary"></span>Loading Departments…
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer: count + pagination --}}
    <div class="card-footer bg-light py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <small class="text-muted font-weight-bold text-uppercase" id="table-count">—</small>
        <nav id="pagination-nav" aria-label="Department pagination"></nav>
    </div>
</div>

{{-- ===== MODAL: ADD / EDIT ===== --}}
<div class="modal fade" id="deptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase" id="deptModalLabel">Add Department</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="modal-id">

                <div class="bg-white border rounded p-4 shadow-sm mb-4">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                                Department Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="modal-name" class="form-control border-secondary font-weight-bold text-dark shadow-sm" placeholder="e.g., Production" oninput="onNameInput()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                                Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="modal-code" class="form-control shadow-sm font-weight-bold text-dark" placeholder="e.g., PROD" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Description</label>
                            <textarea id="modal-description" class="form-control shadow-sm p-3" rows="2" placeholder="Brief description…"></textarea>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Branch</label>
                            <select id="modal-branch" class="form-select shadow-sm font-weight-bold text-dark"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Status</label>
                            <select id="modal-status" class="form-select shadow-sm font-weight-bold text-dark">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Department Heads --}}
                <div class="bg-white border rounded p-4 shadow-sm">
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-3 d-block border-bottom pb-2">Department Head(s)</label>
                    <div id="heads-container" class="d-flex flex-column gap-3 mb-3"></div>
                    <button type="button" class="btn btn-outline-dark btn-sm font-weight-bold px-3 shadow-sm" onclick="addHeadRow()">
                        + Add Another Head
                    </button>
                    <div class="form-text small font-weight-bold text-uppercase mt-3" style="font-size: 0.65rem;">
                        Shows employees with supervisory/managerial positions in this department.
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white py-3">
                <button class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-secondary font-weight-bold px-4 shadow-sm" id="modal-save-btn" onclick="saveDepartment()">Create Department</button>
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
    const BASE = '{{ url("/admin/departments") }}';

    // ─── STATE ───────────────────────────────────────────────────────────────
    let headRows       = [];
    let headCandidates = [];
    let searchTimer    = null;

    // Pagination state
    let allData     = [];
    let currentPage = 1;
    let perPage     = 10;

    // ─── INIT ─────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        loadStats();
        loadBranches();
        loadList();
    });

    // ─── STATS ────────────────────────────────────────────────────────────────
    function loadStats() {
        fetch(BASE + '/stats', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(function (d) {
                setText('stat-total',        d.total);
                setText('stat-active-sub',   d.active + ' active');
                setText('stat-employees',    Number(d.total_employees).toLocaleString());
                setText('stat-largest-name', d.largest_name);
                setText('stat-largest-count',d.largest_count + ' employees');
                setText('stat-branches',     d.branch_count);
                setText('stat-branches-sub', d.main_branch);
            })
            .catch(console.error);
    }

    // ─── BRANCHES ─────────────────────────────────────────────────────────────
    function loadBranches() {
        fetch(BASE + '/branches', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(function (branches) {
                const filterSel = document.getElementById('filter-branch');
                const modalSel  = document.getElementById('modal-branch');

                filterSel.innerHTML = '<option value="">All Branches</option>';
                modalSel.innerHTML  = '<option value="">— None —</option>';

                branches.forEach(function (b) {
                    filterSel.innerHTML += `<option value="${x(b)}">${x(b)}</option>`;
                    modalSel.innerHTML  += `<option value="${x(b)}">${x(b)}</option>`;
                });
            })
            .catch(console.error);
    }

    // ─── LIST ─────────────────────────────────────────────────────────────────
    window.loadList = function () {
        const tbody  = document.getElementById('dept-tbody');
        const search = document.getElementById('filter-search').value;
        const branch = document.getElementById('filter-branch').value;
        const status = document.getElementById('filter-status').value;

        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 bg-white text-muted font-weight-bold text-uppercase"><span class="spinner-border spinner-border-sm me-2 text-secondary"></span>Loading Departments…</td></tr>`;
        document.getElementById('pagination-nav').innerHTML = '';

        const url = BASE + '/list?' + new URLSearchParams({ search, branch, status });

        fetch(url, { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(function (data) {
                allData     = data;
                currentPage = 1;
                renderPage();
            })
            .catch(function () {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 bg-white text-dark font-weight-bold text-uppercase">Failed to load data.</td></tr>`;
            });
    };

    // ─── PAGINATION ───────────────────────────────────────────────────────────
    function renderPage() {
        const totalItems = allData.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));

        // Clamp currentPage
        if (currentPage < 1)          currentPage = 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const start  = (currentPage - 1) * perPage;
        const end    = Math.min(start + perPage, totalItems);
        const sliced = allData.slice(start, end);

        renderTable(sliced, totalItems, start, end);
        renderPagination(totalPages);
    }

    function renderTable(data, totalItems, start, end) {
        const tbody = document.getElementById('dept-tbody');

        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 bg-white text-muted"><span class="font-weight-bold text-dark d-block mb-1">No departments found.</span><small class="text-muted font-weight-bold text-uppercase">Try adjusting your filters.</small></td></tr>`;
            setText('table-count', 'Showing 0 departments');
            return;
        }

        tbody.innerHTML = data.map(function (d) {
            const statusBadge = d.status === 'active'
                ? `<span class="badge bg-secondary px-2 py-1 text-uppercase">Active</span>`
                : `<span class="badge bg-light border text-muted px-2 py-1 text-uppercase">Inactive</span>`;

            const actions = `
                <button class="btn btn-sm btn-light border text-dark font-weight-bold px-2 me-1 shadow-sm" title="Edit" onclick="openEditModal(${d.id})"><i class="bi bi-pencil-fill"></i></button>
                <button class="btn btn-sm btn-outline-dark font-weight-bold px-2" title="Delete" onclick="deleteDept(${d.id}, '${x(d.name)}', ${d.employee_count})"><i class="bi bi-trash-fill"></i></button>
            `;

            return `
            <tr class="border-bottom bg-white">
                <td class="ps-4 py-3">
                    <div class="font-weight-bold text-dark mb-1">${x(d.name)}</div>
                    <div class="text-muted small font-weight-bold text-uppercase" style="font-size:0.65rem">${x(d.description) || '—'}</div>
                </td>
                <td class="py-3"><span class="badge bg-light border text-dark px-2 py-1 text-uppercase">${x(d.code)}</span></td>
                <td class="py-3"><div class="text-dark font-weight-bold small">${x(d.head_names) || '—'}</div></td>
                <td class="py-3 font-weight-bold text-secondary text-center">${d.employee_count}</td>
                <td class="py-3 text-muted small font-weight-bold text-uppercase">${x(d.branch) || '—'}</td>
                <td class="py-3 text-center">${statusBadge}</td>
                <td class="text-center pe-4 py-3 text-nowrap">${actions}</td>
            </tr>`;
        }).join('');

        const showing = totalItems > 0
            ? `Showing ${start + 1}–${end} of ${totalItems} department(s)`
            : 'Showing 0 departments';
        setText('table-count', showing);
    }

    function renderPagination(totalPages) {
        const nav = document.getElementById('pagination-nav');

        if (totalPages <= 1) {
            nav.innerHTML = '';
            return;
        }

        // Build page window: always show first, last, current ±2
        const pages = buildPageWindow(currentPage, totalPages);

        let html = '<ul class="pagination pagination-sm mb-0">';

        // Prev
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link font-weight-bold" href="#" onclick="goToPage(${currentPage - 1}); return false;">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>`;

        let lastPage = null;
        pages.forEach(function (p) {
            if (lastPage !== null && p - lastPage > 1) {
                // Ellipsis
                html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            }
            if (p === currentPage) {
                html += `<li class="page-item active">
                    <span class="page-link font-weight-bold">${p}</span>
                </li>`;
            } else {
                html += `<li class="page-item">
                    <a class="page-link font-weight-bold" href="#" onclick="goToPage(${p}); return false;">${p}</a>
                </li>`;
            }
            lastPage = p;
        });

        // Next
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link font-weight-bold" href="#" onclick="goToPage(${currentPage + 1}); return false;">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>`;

        html += '</ul>';
        nav.innerHTML = html;
    }

    function buildPageWindow(current, total) {
        const delta = 2;
        const range = [];
        const result = [];

        for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
            range.push(i);
        }

        // Always include 1 and total
        if (total === 1) return [1];

        result.push(1);
        range.forEach(function (p) { result.push(p); });
        if (total > 1) result.push(total);

        // Dedupe & sort
        return [...new Set(result)].sort(function (a, b) { return a - b; });
    }

    window.goToPage = function (page) {
        currentPage = page;
        renderPage();
    };

    window.onPerPageChange = function () {
        perPage     = parseInt(document.getElementById('filter-per-page').value, 10);
        currentPage = 1;
        renderPage();
    };

    // ─── HEAD ROWS ────────────────────────────────────────────────────────────
    window.loadHeadCandidates = function (deptName, callback) {
        const url = BASE + '/head-candidates?' + new URLSearchParams({ department: deptName || '' });
        fetch(url, { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(function (data) {
                headCandidates = data;
                if (callback) callback();
            })
            .catch(console.error);
    };

    window.renderHeadRows = function () {
        const container = document.getElementById('heads-container');

        if (!headRows.length) headRows = [{ value: '' }];

        container.innerHTML = headRows.map(function (row, idx) {
            const options = headCandidates.map(function (c) {
                const sel = row.value === String(c.id) ? ' selected' : '';
                return `<option value="${x(c.id)}"${sel}>${x(c.full_name)} — ${x(c.position)}</option>`;
            }).join('');

            const removeBtn = headRows.length > 1
                ? `<button class="btn btn-outline-dark font-weight-bold px-3 shadow-sm" type="button" onclick="removeHeadRow(${idx})"><i class="bi bi-x-lg"></i></button>`
                : '';

            return `
                <div class="input-group shadow-sm">
                    <select class="form-select font-weight-bold text-dark border-secondary" onchange="headRows[${idx}].value = this.value">
                        <option value="">— Select Head —</option>
                        ${options}
                    </select>
                    ${removeBtn}
                </div>`;
        }).join('');
    };

    window.addHeadRow = function () {
        headRows.push({ value: '' });
        renderHeadRows();
    };

    window.removeHeadRow = function (idx) {
        headRows.splice(idx, 1);
        renderHeadRows();
    };

    window.onNameInput = function () {
        const name = document.getElementById('modal-name').value.trim();
        loadHeadCandidates(name, renderHeadRows);
    };

    // ─── ADD MODAL ────────────────────────────────────────────────────────────
    window.openAddModal = function () {
        setText('deptModalLabel', 'Add Department');
        document.getElementById('modal-save-btn').textContent = 'Create Department';
        document.getElementById('modal-id').value          = '';
        document.getElementById('modal-name').value        = '';
        document.getElementById('modal-code').value        = '';
        document.getElementById('modal-description').value = '';
        document.getElementById('modal-status').value      = 'active';

        headRows       = [{ value: '' }];
        headCandidates = [];
        renderHeadRows();

        bootstrap.Modal.getOrCreateInstance(document.getElementById('deptModal')).show();
    };

    // ─── EDIT MODAL ───────────────────────────────────────────────────────────
    window.openEditModal = function (id) {
        const d = allData.find(function (r) { return r.id === id; });
        if (!d) return;

        setText('deptModalLabel', 'Edit Department');
        document.getElementById('modal-save-btn').textContent = 'Save Changes';
        document.getElementById('modal-id').value          = d.id;
        document.getElementById('modal-name').value        = d.name;
        document.getElementById('modal-code').value        = d.code;
        document.getElementById('modal-description').value = d.description;
        document.getElementById('modal-status').value      = d.status;

        setTimeout(function () {
            document.getElementById('modal-branch').value = d.branch !== '—' ? d.branch : '';
        }, 50);

        headRows = (d.head_ids || []).map(function (v) { return { value: String(v) }; });
        if (!headRows.length) headRows = [{ value: '' }];

        loadHeadCandidates(d.name, renderHeadRows);

        bootstrap.Modal.getOrCreateInstance(document.getElementById('deptModal')).show();
    };

    // ─── SAVE ─────────────────────────────────────────────────────────────────
    window.saveDepartment = function () {
        const id          = document.getElementById('modal-id').value;
        const name        = document.getElementById('modal-name').value.trim();
        const code        = document.getElementById('modal-code').value.trim();
        const description = document.getElementById('modal-description').value.trim();
        const branch      = document.getElementById('modal-branch').value;
        const status      = document.getElementById('modal-status').value;
        const headIds     = headRows.map(function (r) { return r.value; }).filter(Boolean);

        if (!name) { toast('Department name is required.', 'warning'); return; }
        if (!code) { toast('Department code is required.', 'warning'); return; }

        const btn     = document.getElementById('modal-save-btn');
        btn.disabled  = true;
        const origTxt = btn.textContent;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

        fetch(id ? BASE + '/' + id : BASE, {
            method:  id ? 'PATCH' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ name, code, description, branch, status, head_employee_ids: headIds }),
        })
        .then(handleJson)
        .then(function (res) {
            bootstrap.Modal.getInstance(document.getElementById('deptModal')).hide();
            toast(res.message);
            loadStats();
            loadList();
        })
        .catch(handleError)
        .finally(function () {
            btn.disabled    = false;
            btn.textContent = origTxt;
        });
    };

    // ─── DELETE ───────────────────────────────────────────────────────────────
    window.deleteDept = function (id, name, empCount) {
        if (empCount > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Cannot Delete',
                text: '"' + name + '" has ' + empCount + ' active employee(s). Reassign them first.',
                confirmButtonColor: '#1a1a1a'
            });
            return;
        }

        Swal.fire({
            title: 'Delete Department?',
            html: '<strong>' + x(name) + '</strong> will be permanently removed.',
            icon: 'question',
            showCancelButton:  true,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor:'#1a1a1a',
            cancelButtonColor: '#6c757d',
        }).then(function (result) {
            if (!result.isConfirmed) return;

            fetch(BASE + '/' + id, {
                method:  'DELETE',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': CSRF },
            })
            .then(handleJson)
            .then(function (res) {
                toast(res.message);
                loadStats();
                loadList();
            })
            .catch(handleError);
        });
    };

    // ─── FILTERS ──────────────────────────────────────────────────────────────
    window.resetFilters = function () {
        document.getElementById('filter-search').value   = '';
        document.getElementById('filter-branch').value   = '';
        document.getElementById('filter-status').value   = '';
        document.getElementById('filter-per-page').value = '10';
        perPage = 10;
        loadList();
    };

    window.debounceList = function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadList, 350);
    };

    // ─── UTILITIES ────────────────────────────────────────────────────────────
    function handleJson(r) {
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

    function toast(msg, icon) {
        Swal.fire({
            toast: true, position: 'top-end',
            icon: icon || 'success', title: msg,
            showConfirmButton: false, timer: 2800, timerProgressBar: true,
        });
    }

    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val !== null && val !== undefined ? val : '—';
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