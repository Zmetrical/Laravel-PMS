@extends('layouts.main')

@section('title', 'Position Management')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="#" class="text-secondary text-decoration-none">Organization</a></li>
        <li class="breadcrumb-item active text-muted">Positions</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Position Management</h4>
        <small class="text-muted font-weight-bold text-uppercase">Manage job positions and their departments</small>
    </div>
    <button class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm" onclick="openAddModal()">
        Add Position
    </button>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-5">
    <div class="col-12 col-sm-4">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Total Positions</span>
            <span class="h2 font-weight-bold text-dark mb-0" id="stat-total">
                <span class="spinner-border spinner-border-sm text-secondary"></span>
            </span>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Active Positions</span>
            <span class="h2 font-weight-bold text-dark mb-0" id="stat-active">
                <span class="spinner-border spinner-border-sm text-secondary"></span>
            </span>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="border border-secondary rounded bg-light p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-dark small font-weight-bold text-uppercase mb-2">Departments Covered</span>
            <span class="h2 font-weight-bold text-dark mb-0" id="stat-depts">
                <span class="spinner-border spinner-border-sm text-secondary"></span>
            </span>
        </div>
    </div>
</div>

{{-- Filters + Table --}}
<div class="card shadow-sm border-0 mb-5">

    {{-- Filters --}}
    <div class="card-body bg-light border-bottom p-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="filter-search" class="form-control border-start-0 font-weight-bold text-dark ps-0"
                        placeholder="Search position or department…" oninput="debounceList()">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filter-dept" class="form-select shadow-sm font-weight-bold text-dark" onchange="loadList()">
                    <option value="">All Departments</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="filter-status" class="form-select shadow-sm font-weight-bold text-dark" onchange="loadList()">
                    <option value="">All Statuses</option>
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

    {{-- Table --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 ps-4 py-3">Position</th>
                        <th class="border-0 py-3">Department</th>
                        <th class="border-0 py-3 d-none d-md-table-cell">Description</th>
                        <th class="border-0 py-3 text-center">Employees</th>
                        <th class="border-0 py-3 text-center">Status</th>
                        <th class="border-0 py-3 text-center pe-4" style="width:120px">Actions</th>
                    </tr>
                </thead>
                <tbody id="positions-tbody">
                    <tr>
                        <td colspan="6" class="text-center py-5 bg-white text-muted font-weight-bold text-uppercase">
                            <span class="spinner-border spinner-border-sm me-2 text-secondary"></span>Loading Positions…
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer: count + pagination --}}
    <div class="card-footer bg-light py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <small class="text-muted font-weight-bold text-uppercase" id="table-count">—</small>
        <nav id="pagination-nav" aria-label="Position pagination"></nav>
    </div>
</div>

{{-- ===== MODAL: ADD / EDIT (shared) ===== --}}
<div class="modal fade" id="posModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase" id="posModalLabel">Add Position</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="modal-id">

                <div class="bg-white border rounded p-4 shadow-sm mb-4">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                                Position Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="modal-name" class="form-control border-secondary font-weight-bold text-dark shadow-sm" placeholder="e.g., Production Operator">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                                Department <span class="text-danger">*</span>
                            </label>
                            <select id="modal-dept" class="form-select shadow-sm font-weight-bold text-dark">
                                <option value="">Select Department</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Status</label>
                            <select id="modal-status" class="form-select shadow-sm font-weight-bold text-dark">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Description</label>
                            <textarea id="modal-description" class="form-control shadow-sm p-3" rows="3" placeholder="Brief description of the position…"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white py-3">
                <button class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-secondary font-weight-bold px-4 shadow-sm" id="modal-save-btn" onclick="savePosition()">Add Position</button>
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
    const BASE = '{{ url("/admin/positions") }}';

    // ─── STATE ───────────────────────────────────────────────────────────────
    let searchTimer = null;

    // Pagination state
    let allData     = [];
    let currentPage = 1;
    let perPage     = 10;

    // ─── INIT ─────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        loadStats();
        loadDepartments();
        loadList();
    });

    // ─── STATS ────────────────────────────────────────────────────────────────
    function loadStats() {
        fetch(BASE + '/stats', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(function (d) {
                setText('stat-total',  d.total);
                setText('stat-active', d.active);
                setText('stat-depts',  d.depts);
            })
            .catch(console.error);
    }

    // ─── DEPARTMENTS ──────────────────────────────────────────────────────────
    function loadDepartments() {
        fetch(BASE + '/departments', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(function (depts) {
                const opts = depts.map(d => `<option value="${x(d)}">${x(d)}</option>`).join('');

                document.getElementById('filter-dept').innerHTML = `<option value="">All Departments</option>${opts}`;
                document.getElementById('modal-dept').innerHTML  = `<option value="">Select Department</option>${opts}`;
            })
            .catch(console.error);
    }

    // ─── LIST ─────────────────────────────────────────────────────────────────
    window.loadList = function () {
        const tbody  = document.getElementById('positions-tbody');
        const search = document.getElementById('filter-search').value;
        const dept   = document.getElementById('filter-dept').value;
        const status = document.getElementById('filter-status').value;

        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 bg-white text-muted font-weight-bold text-uppercase"><span class="spinner-border spinner-border-sm me-2 text-secondary"></span>Loading Positions…</td></tr>`;
        document.getElementById('pagination-nav').innerHTML = '';

        const url = BASE + '/list?' + new URLSearchParams({ search, department: dept, status });

        fetch(url, { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(function (data) {
                allData     = data;
                currentPage = 1;
                renderPage();
            })
            .catch(function () {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 bg-white text-dark font-weight-bold text-uppercase">Failed to load data.</td></tr>`;
            });
    };

    // ─── PAGINATION ───────────────────────────────────────────────────────────
    function renderPage() {
        const totalItems = allData.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));

        if (currentPage < 1)          currentPage = 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const start  = (currentPage - 1) * perPage;
        const end    = Math.min(start + perPage, totalItems);
        const sliced = allData.slice(start, end);

        renderTable(sliced, totalItems, start, end);
        renderPagination(totalPages);
    }

    function renderTable(data, totalItems, start, end) {
        const tbody = document.getElementById('positions-tbody');

        if (!data.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 bg-white text-muted"><span class="font-weight-bold text-dark d-block mb-1">No positions found.</span><small class="text-muted font-weight-bold text-uppercase">Try adjusting your filters.</small></td></tr>`;
            setText('table-count', 'Showing 0 positions');
            return;
        }

        tbody.innerHTML = data.map(function (p) {
            const statusBadge = p.status === 'active'
                ? `<span class="badge bg-secondary px-2 py-1 text-uppercase">Active</span>`
                : `<span class="badge bg-light border text-muted px-2 py-1 text-uppercase">Inactive</span>`;

            const actions = `
                <button class="btn btn-sm btn-light border text-dark font-weight-bold px-2 me-1 shadow-sm" title="Edit" onclick="openEditModal(${p.id})"><i class="bi bi-pencil-fill"></i></button>
                <button class="btn btn-sm btn-outline-dark font-weight-bold px-2" title="Delete" onclick="deletePosition(${p.id}, '${x(p.name)}', ${p.employee_count})"><i class="bi bi-trash-fill"></i></button>
            `;

            return `
            <tr class="border-bottom bg-white">
                <td class="ps-4 py-3 font-weight-bold text-dark">${x(p.name)}</td>
                <td class="py-3 text-secondary font-weight-bold text-uppercase" style="font-size: 0.75rem;">${x(p.department)}</td>
                <td class="d-none d-md-table-cell py-3 text-muted small font-weight-bold">${x(p.description || '—')}</td>
                <td class="py-3 text-center font-weight-bold text-dark">${p.employee_count}</td>
                <td class="py-3 text-center">${statusBadge}</td>
                <td class="text-center pe-4 py-3 text-nowrap">${actions}</td>
            </tr>`;
        }).join('');

        const showing = totalItems > 0
            ? `Showing ${start + 1}–${end} of ${totalItems} position(s)`
            : 'Showing 0 positions';
        setText('table-count', showing);
    }

    function renderPagination(totalPages) {
        const nav = document.getElementById('pagination-nav');

        if (totalPages <= 1) {
            nav.innerHTML = '';
            return;
        }

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
                html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            }
            if (p === currentPage) {
                html += `<li class="page-item active"><span class="page-link font-weight-bold">${p}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link font-weight-bold" href="#" onclick="goToPage(${p}); return false;">${p}</a></li>`;
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

        for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
            range.push(i);
        }

        if (total === 1) return [1];

        const result = [1, ...range];
        if (total > 1) result.push(total);

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

    // ─── ADD MODAL ────────────────────────────────────────────────────────────
    window.openAddModal = function () {
        setText('posModalLabel', 'Add Position');
        document.getElementById('modal-save-btn').textContent = 'Add Position';
        document.getElementById('modal-id').value          = '';
        document.getElementById('modal-name').value        = '';
        document.getElementById('modal-dept').value        = '';
        document.getElementById('modal-description').value = '';
        document.getElementById('modal-status').value      = 'active';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('posModal')).show();
    };

    // ─── EDIT MODAL ───────────────────────────────────────────────────────────
    window.openEditModal = function (id) {
        // Find directly from allData — no extra fetch needed
        const p = allData.find(function (r) { return r.id === id; });
        if (!p) return;

        setText('posModalLabel', 'Edit Position');
        document.getElementById('modal-save-btn').textContent = 'Save Changes';
        document.getElementById('modal-id').value          = p.id;
        document.getElementById('modal-name').value        = p.name;
        document.getElementById('modal-description').value = p.description;
        document.getElementById('modal-status').value      = p.status;

        setTimeout(function () {
            document.getElementById('modal-dept').value = p.department !== '—' ? p.department : '';
        }, 50);

        bootstrap.Modal.getOrCreateInstance(document.getElementById('posModal')).show();
    };

    // ─── SAVE ─────────────────────────────────────────────────────────────────
    window.savePosition = function () {
        const id   = document.getElementById('modal-id').value;
        const name = document.getElementById('modal-name').value.trim();
        const dept = document.getElementById('modal-dept').value;
        const desc = document.getElementById('modal-description').value.trim();
        const stat = document.getElementById('modal-status').value;

        if (!name) { toast('Position name is required.', 'warning'); return; }
        if (!dept) { toast('Department is required.', 'warning');     return; }

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
            body: JSON.stringify({ name, department: dept, description: desc, status: stat }),
        })
        .then(handleJson)
        .then(function (res) {
            bootstrap.Modal.getInstance(document.getElementById('posModal')).hide();
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
    window.deletePosition = function (id, name, empCount) {
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
            title: 'Delete Position?',
            html: '<strong>' + x(name) + '</strong> will be permanently removed.',
            icon: 'question',
            showCancelButton:  true,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor:'#1a1a1a',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
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
        document.getElementById('filter-dept').value     = '';
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