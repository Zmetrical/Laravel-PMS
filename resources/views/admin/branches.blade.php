@extends('layouts.main')

@section('title', 'Branch Management')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="#" class="text-secondary text-decoration-none">Organization</a></li>
        <li class="breadcrumb-item active text-muted">Branches</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Branch Management</h4>
        <small class="text-muted font-weight-bold text-uppercase">Manage company branches and locations</small>
    </div>
    <button class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm" onclick="openAddModal()">
        Add Branch
    </button>
</div>

{{-- Stats --}}
<div class="row g-3 mb-5">
    @foreach ([
        ['id' => 'stat-total',  'label' => 'Total Branches',  'sub_id' => 'stat-active-sub'],
        ['id' => 'stat-emp',    'label' => 'Total Employees',  'sub' => 'Across all branches'],
        ['id' => 'stat-depts',  'label' => 'Departments',      'sub' => 'Total departments'],
        ['id' => 'stat-cities', 'label' => 'Cities Covered',   'sub' => 'Unique locations'],
    ] as $c)
    <div class="col-6 col-xl-3">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">{{ $c['label'] }}</span>
            <span class="h2 font-weight-bold text-dark mb-1" id="{{ $c['id'] }}">
                <span class="spinner-border spinner-border-sm text-secondary"></span>
            </span>
            @if(isset($c['sub_id']))
                <span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;" id="{{ $c['sub_id'] }}">Loading...</span>
            @else
                <span class="text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;">{{ $c['sub'] }}</span>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- Branch Grid --}}
<div class="row g-4" id="branchGrid">
    <div class="col-12 text-center py-5">
        <span class="spinner-border spinner-border-sm text-secondary me-2"></span>
        <span class="font-weight-bold text-muted text-uppercase small">Loading Branches...</span>
    </div>
</div>

<div id="emptyState" class="text-center py-5 bg-white border rounded shadow-sm d-none">
    <i class="bi bi-geo-alt text-muted mb-3 d-block" style="font-size: 3rem;"></i>
    <span class="font-weight-bold text-dark d-block mb-1">No Branches Found</span>
    <small class="text-muted font-weight-bold text-uppercase">Add your first branch to get started.</small>
</div>

{{-- ===== MODAL: ADD / EDIT ===== --}}
<div class="modal fade" id="branchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase" id="branchModalLabel">Add New Branch</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="modal-id">
                
                <div class="bg-white border rounded p-4 shadow-sm mb-4">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                                Branch Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="modal-name" class="form-control border-secondary font-weight-bold text-dark shadow-sm" placeholder="e.g., Meycauayan Main Office">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                                Branch Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="modal-code" class="form-control shadow-sm font-weight-bold text-dark" placeholder="e.g., MYC-MAIN" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Address <span class="text-danger">*</span></label>
                            <input type="text" id="modal-address" class="form-control shadow-sm font-weight-bold text-dark" placeholder="Street, Barangay">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">City / Municipality <span class="text-danger">*</span></label>
                            <input type="text" id="modal-city" class="form-control shadow-sm font-weight-bold text-dark" placeholder="e.g., Meycauayan, Bulacan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" id="modal-contact" class="form-control shadow-sm font-weight-bold text-dark" placeholder="+63 XX XXX XXXX">
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded p-4 shadow-sm mb-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Email Address</label>
                            <input type="email" id="modal-email" class="form-control shadow-sm font-weight-bold text-dark" placeholder="branch@company.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Branch Manager <span class="text-danger">*</span></label>
                            <input type="text" id="modal-manager" class="form-control shadow-sm font-weight-bold text-dark" placeholder="Manager's full name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Status</label>
                            <select id="modal-status" class="form-select shadow-sm font-weight-bold text-dark">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end pb-2">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input border-secondary" type="checkbox" id="modal-is-main" style="cursor:pointer;">
                                <label class="form-check-label small font-weight-bold text-uppercase text-dark ms-1" for="modal-is-main" style="cursor:pointer;">Set as Main Branch</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white py-3">
                <button class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-secondary font-weight-bold px-4 shadow-sm" id="modal-save-btn" onclick="saveBranch()">Save Branch</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const CSRF = '{{ csrf_token() }}';
    const BASE = '{{ url("/admin/branches") }}';
    const REQUIRED = ['modal-name', 'modal-code', 'modal-address', 'modal-city', 'modal-contact', 'modal-manager'];

    document.addEventListener('DOMContentLoaded', function () {
        loadStats();
        loadBranches();
    });

    function loadStats() {
        fetch(BASE + '/stats', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(function (d) {
                setText('stat-total',      d.total);
                setText('stat-active-sub', d.active + ' active');
                setText('stat-emp',        Number(d.total_emp).toLocaleString());
                setText('stat-depts',      d.total_depts);
                setText('stat-cities',     d.cities);
            })
            .catch(console.error);
    }

    function loadBranches() {
        const grid  = document.getElementById('branchGrid');
        const empty = document.getElementById('emptyState');

        grid.innerHTML = `<div class="col-12 text-center py-5"><span class="spinner-border spinner-border-sm text-secondary me-2"></span><span class="font-weight-bold text-muted text-uppercase small">Loading Branches...</span></div>`;
        empty.classList.add('d-none');

        fetch(BASE + '/list', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(function (data) {
                if (!data.length) {
                    grid.innerHTML = '';
                    empty.classList.remove('d-none');
                    return;
                }
                grid.innerHTML = data.map(buildCard).join('');
            })
            .catch(function () {
                grid.innerHTML = `<div class="col-12 text-center py-5 bg-white border rounded shadow-sm text-dark font-weight-bold">Failed to load branches.</div>`;
            });
    }

    function buildCard(b) {
        const statusBadge = b.status === 'active'
            ? `<span class="badge bg-secondary px-2 py-1 text-uppercase">Active</span>`
            : `<span class="badge bg-light border text-muted px-2 py-1 text-uppercase">Inactive</span>`;

        const mainBadge = b.is_main
            ? `<span class="badge bg-dark text-white ms-2 text-uppercase" style="font-size:0.6rem; letter-spacing: 0.5px;">Main</span>`
            : '';

        return `
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="font-weight-bold text-dark text-uppercase mb-1" style="font-size: 1.05rem;">${x(b.name)}${mainBadge}</div>
                            <span class="badge bg-light border text-dark text-uppercase px-2 py-1" style="font-size: 0.65rem;">${x(b.code)}</span>
                        </div>
                        ${statusBadge}
                    </div>

                    <div class="bg-light border rounded p-3 mb-4">
                        <div class="d-flex gap-3 mb-2">
                            <i class="bi bi-geo-alt-fill text-muted"></i>
                            <div class="small font-weight-bold text-dark">
                                ${x(b.address)}<br>
                                <span class="text-secondary text-uppercase" style="font-size: 0.7rem;">${x(b.city)}</span>
                            </div>
                        </div>
                        <div class="d-flex gap-3 mb-2">
                            <i class="bi bi-telephone-fill text-muted"></i>
                            <div class="small font-weight-bold text-dark">${x(b.contact_number)}</div>
                        </div>
                        ${b.email ? `
                        <div class="d-flex gap-3 mb-2">
                            <i class="bi bi-envelope-fill text-muted"></i>
                            <div class="small font-weight-bold text-dark text-truncate">${x(b.email)}</div>
                        </div>` : ''}
                        <div class="d-flex gap-3">
                            <i class="bi bi-person-badge-fill text-muted"></i>
                            <div class="small font-weight-bold text-dark">
                                <span class="text-muted text-uppercase" style="font-size: 0.65rem;">Manager:</span> ${x(b.manager_name)}
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 text-center mb-4 mt-auto">
                        <div class="col-6">
                            <div class="border rounded bg-white py-2 shadow-sm">
                                <div class="font-weight-bold text-dark h5 mb-0">${b.employee_count}</div>
                                <div class="text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;">Employees</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded bg-white py-2 shadow-sm">
                                <div class="font-weight-bold text-dark h5 mb-0">${b.dept_count}</div>
                                <div class="text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;">Departments</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-dark font-weight-bold btn-sm flex-grow-1 py-2" onclick="openEditModal(${b.id})">Edit Branch</button>
                        <button class="btn btn-outline-dark btn-sm px-3 py-2" onclick="deleteBranch(${b.id}, '${x(b.name)}', ${b.employee_count}, ${b.is_main})" title="Delete">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
    }

    window.openAddModal = function () {
        resetForm();
        setText('branchModalLabel', 'Add New Branch');
        document.getElementById('modal-save-btn').textContent = 'Save Branch';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('branchModal')).show();
    };

    window.openEditModal = function (id) {
        fetch(BASE + '/list', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(function (data) {
                const b = data.find(r => r.id === id);
                if (!b) return;

                resetForm();
                setText('branchModalLabel', 'Edit Branch');
                document.getElementById('modal-save-btn').textContent = 'Update Branch';
                document.getElementById('modal-id').value       = b.id;
                document.getElementById('modal-name').value     = b.name;
                document.getElementById('modal-code').value     = b.code;
                document.getElementById('modal-address').value  = b.address;
                document.getElementById('modal-city').value     = b.city;
                document.getElementById('modal-contact').value  = b.contact_number;
                document.getElementById('modal-email').value    = b.email;
                document.getElementById('modal-manager').value  = b.manager_name;
                document.getElementById('modal-status').value   = b.status;
                document.getElementById('modal-is-main').checked = b.is_main;

                bootstrap.Modal.getOrCreateInstance(document.getElementById('branchModal')).show();
            });
    };

    window.saveBranch = function () {
        if (!validateForm()) return;

        const id   = document.getElementById('modal-id').value;
        const body = {
            name:           document.getElementById('modal-name').value.trim(),
            code:           document.getElementById('modal-code').value.trim().toUpperCase(),
            address:        document.getElementById('modal-address').value.trim(),
            city:           document.getElementById('modal-city').value.trim(),
            contact_number: document.getElementById('modal-contact').value.trim(),
            email:          document.getElementById('modal-email').value.trim(),
            manager_name:   document.getElementById('modal-manager').value.trim(),
            status:         document.getElementById('modal-status').value,
            is_main:        document.getElementById('modal-is-main').checked,
        };

        const btn     = document.getElementById('modal-save-btn');
        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

        fetch(id ? BASE + '/' + id : BASE, {
            method:  id ? 'PATCH' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(body),
        })
        .then(handleJson)
        .then(function (res) {
            bootstrap.Modal.getInstance(document.getElementById('branchModal')).hide();
            toast(res.message);
            loadStats();
            loadBranches();
        })
        .catch(handleError)
        .finally(function () {
            btn.disabled    = false;
            btn.textContent = 'Save Branch';
        });
    };

    window.deleteBranch = function (id, name, empCount, isMain) {
        if (isMain) {
            Swal.fire({ icon: 'warning', title: 'Cannot Delete', text: 'Cannot delete the main branch. Set another branch as main first.', confirmButtonColor: '#1a1a1a' });
            return;
        }
        if (empCount > 0) {
            Swal.fire({ icon: 'warning', title: 'Cannot Delete', text: '"' + name + '" has ' + empCount + ' active employee(s). Reassign them first.', confirmButtonColor: '#1a1a1a' });
            return;
        }

        Swal.fire({
            title: 'Delete Branch?',
            html:  `<strong>${x(name)}</strong> will be permanently removed.`,
            icon:  'question',
            showCancelButton:   true,
            confirmButtonText:  'Yes, Delete',
            confirmButtonColor: '#1a1a1a',
            cancelButtonColor:  '#6c757d',
        }).then(function (result) {
            if (!result.isConfirmed) return;

            fetch(BASE + '/' + id, {
                method: 'DELETE',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': CSRF },
            })
            .then(handleJson)
            .then(function (res) {
                toast(res.message);
                loadStats();
                loadBranches();
            })
            .catch(handleError);
        });
    };

    function resetForm() {
        document.getElementById('modal-id').value        = '';
        document.getElementById('modal-name').value      = '';
        document.getElementById('modal-code').value      = '';
        document.getElementById('modal-address').value   = '';
        document.getElementById('modal-city').value      = '';
        document.getElementById('modal-contact').value   = '';
        document.getElementById('modal-email').value     = '';
        document.getElementById('modal-manager').value   = '';
        document.getElementById('modal-status').value    = 'active';
        document.getElementById('modal-is-main').checked = false;
        REQUIRED.forEach(id => document.getElementById(id).classList.remove('is-invalid'));
    }

    function validateForm() {
        let valid = true;
        REQUIRED.forEach(id => {
            const el = document.getElementById(id);
            if (!el.value.trim()) {
                el.classList.add('is-invalid');
                valid = false;
            } else {
                el.classList.remove('is-invalid');
            }
        });
        return valid;
    }

    function handleJson(r) {
        return r.json().then(data => {
            if (!r.ok) return Promise.reject(data);
            return data;
        });
    }

    function handleError(err) {
        Swal.fire({ icon: 'error', title: 'Error', text: err && err.message ? err.message : 'Something went wrong.', confirmButtonColor: '#1a1a1a' });
    }

    function toast(msg) {
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: msg, showConfirmButton: false, timer: 2800, timerProgressBar: true });
    }

    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val !== null && val !== undefined ? val : '—';
    }

    function x(str) {
        if (str == null) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

})();
</script>
@endpush