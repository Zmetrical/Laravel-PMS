@extends('layouts.main')

@section('title', 'Pending Requests')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">Pending Requests</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Pending Requests</h4>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-5">
    @foreach ([
        ['id' => 'statTotal',   'label' => 'Total Pending',    'sub' => 'Requires action'],
        ['id' => 'statLeave',   'label' => 'Leave Requests',   'sub' => 'Awaiting approval'],
        ['id' => 'statOT',      'label' => 'Overtime',         'sub' => 'Awaiting approval'],
        ['id' => 'statProfile', 'label' => 'Profile Updates',  'sub' => 'Awaiting review'],
    ] as $card)
    <div class="col-6 col-md-3">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">{{ $card['label'] }}</span>
            <span class="h2 font-weight-bold text-dark mb-1" id="{{ $card['id'] }}">
                <span class="spinner-border spinner-border-sm text-secondary" style="width: 1.5rem; height: 1.5rem;"></span>
            </span>
            <span class="text-muted" style="font-size: 0.7rem;">{{ $card['sub'] }}</span>
        </div>
    </div>
    @endforeach
</div>

{{-- Main Card with Tabs --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white p-0 border-bottom-0">
        <ul class="nav nav-tabs px-4 pt-3 border-bottom" id="reqTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active font-weight-bold text-uppercase py-3 text-secondary" id="tab-leave-btn" data-bs-toggle="tab" href="#tabLeave" role="tab">
                    Leave <span class="badge bg-secondary ms-2" id="bLeave">—</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link font-weight-bold text-uppercase py-3 text-secondary" id="tab-ot-btn" data-bs-toggle="tab" href="#tabOvertime" role="tab">
                    Overtime <span class="badge bg-secondary ms-2" id="bOT">—</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link font-weight-bold text-uppercase py-3 text-secondary" id="tab-profile-btn" data-bs-toggle="tab" href="#tabProfile" role="tab">
                    Profile Updates <span class="badge bg-secondary ms-2" id="bProfile">—</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link font-weight-bold text-uppercase py-3 text-secondary" id="tab-history-btn" data-bs-toggle="tab" href="#tabHistory" role="tab">
                    History
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content">

            {{-- ===== LEAVE ===== --}}
            <div class="tab-pane fade show active" id="tabLeave" role="tabpanel">
                <div id="leaveContainer"></div>
            </div>

            {{-- ===== OVERTIME ===== --}}
            <div class="tab-pane fade" id="tabOvertime" role="tabpanel">
                <div id="overtimeContainer"></div>
            </div>

            {{-- ===== PROFILE UPDATES ===== --}}
            <div class="tab-pane fade" id="tabProfile" role="tabpanel">
                <div id="profileContainer"></div>
            </div>

            {{-- ===== HISTORY ===== --}}
            <div class="tab-pane fade" id="tabHistory" role="tabpanel">
                <div class="p-4 bg-light border-bottom">
                    <div class="d-flex gap-2 flex-wrap" id="historyFilters">
                        <button class="btn btn-secondary font-weight-bold px-4 shadow-sm" data-hfilter="all" onclick="loadHistory('all')">All</button>
                        <button class="btn btn-outline-dark font-weight-bold px-4" data-hfilter="leave" onclick="loadHistory('leave')">Leave</button>
                        <button class="btn btn-outline-dark font-weight-bold px-4" data-hfilter="overtime" onclick="loadHistory('overtime')">Overtime</button>
                        <button class="btn btn-outline-dark font-weight-bold px-4" data-hfilter="profile" onclick="loadHistory('profile')">Profile</button>
                    </div>
                </div>
                <div id="historyContainer"></div>
            </div>

        </div>
    </div>
</div>

{{-- ===== MODAL: REJECT (shared) ===== --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase">Reject Request</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="border border-secondary rounded p-3 mb-4 bg-white shadow-sm font-weight-bold text-dark text-uppercase small" id="rejectDetails"></div>
                
                <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                    Rejection Reason <span class="text-danger">*</span>
                </label>
                <textarea class="form-control shadow-sm p-3 border-secondary" id="rejectReason" rows="4"
                    placeholder="Provide a clear reason visible to the employee…"></textarea>
                <div class="invalid-feedback font-weight-bold mt-2">Please enter a rejection reason.</div>
                <div class="form-text small font-weight-bold text-uppercase mt-2">This reason will be recorded and shown to the employee.</div>
            </div>
            <div class="modal-footer bg-white py-3">
                <button type="button" class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-secondary font-weight-bold px-4 shadow-sm" id="confirmRejectBtn">Confirm Rejection</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL: LEAVE DETAIL ===== --}}
<div class="modal fade" id="leaveDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase">Leave Request Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light" id="leaveDetailBody"></div>
            <div class="modal-footer bg-white py-3">
                <button type="button" class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline-dark font-weight-bold px-4" id="detailRejectBtn">Reject</button>
                <button type="button" class="btn btn-secondary font-weight-bold px-4 shadow-sm" id="detailApproveBtn">Approve</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ─────────────────────────────────────────────────────────────────────────
    // CONFIG
    // ─────────────────────────────────────────────────────────────────────────
    const CSRF = '{{ csrf_token() }}';
    const BASE = '{{ url("/hresource/requests") }}';

    // ─────────────────────────────────────────────────────────────────────────
    // STATE
    // ─────────────────────────────────────────────────────────────────────────
    let rejectState   = null;  
    let detailState   = null;  
    let tabsLoaded    = { leave: false, overtime: false, profile: false, history: false };

    // ─────────────────────────────────────────────────────────────────────────
    // INIT
    // ─────────────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        loadCounts();
        loadLeave();
        tabsLoaded.leave = true;

        document.querySelectorAll('#reqTabs .nav-link').forEach(function (tab) {
            tab.addEventListener('shown.bs.tab', function () {
                const target = this.getAttribute('href');
                if (target === '#tabOvertime' && !tabsLoaded.overtime) {
                    loadOvertime();
                    tabsLoaded.overtime = true;
                }
                if (target === '#tabProfile' && !tabsLoaded.profile) {
                    loadProfile();
                    tabsLoaded.profile = true;
                }
                if (target === '#tabHistory' && !tabsLoaded.history) {
                    loadHistory('all');
                    tabsLoaded.history = true;
                }
            });
        });

        document.getElementById('confirmRejectBtn').addEventListener('click', submitReject);
    });

    // ─────────────────────────────────────────────────────────────────────────
    // COUNTS
    // ─────────────────────────────────────────────────────────────────────────
    function loadCounts() {
        fetch(BASE + '/pending-counts')
            .then(function (r) { return r.json(); })
            .then(function (d) {
                setText('statTotal',   d.total);
                setText('statLeave',   d.leave);
                setText('statOT',      d.overtime);
                setText('statProfile', d.profile);
                setText('bLeave',      d.leave);
                setText('bOT',         d.overtime);
                setText('bProfile',    d.profile);
            })
            .catch(console.error);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOAD: LEAVE
    // ─────────────────────────────────────────────────────────────────────────
    function loadLeave() {
        const c = document.getElementById('leaveContainer');
        c.innerHTML = spinner();

        fetch(BASE + '/leave')
            .then(r => r.json())
            .then(function (data) {
                if (!data.length) { c.innerHTML = emptyState('No pending leave requests.'); return; }

                let html = `
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="border-0 ps-4 py-3">Employee</th>
                                <th class="border-0 py-3">Leave Type</th>
                                <th class="border-0 py-3">Period</th>
                                <th class="border-0 py-3 text-center">Days</th>
                                <th class="border-0 py-3">Submitted</th>
                                <th class="border-0 py-3 pe-4 text-center" style="width:200px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                html += data.map(function (r) {
                    return `
                    <tr class="border-bottom bg-white">
                        <td class="ps-4 py-3">
                            <div class="font-weight-bold text-dark">${x(r.employee)}</div>
                            <div class="text-muted small font-weight-bold text-uppercase" style="font-size:0.7rem">${x(r.employee_id)}</div>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-light border text-dark px-2 py-1 text-uppercase">${x(r.leave_type)}</span>
                        </td>
                        <td class="text-nowrap font-weight-bold text-secondary py-3">${x(r.start_date)} – ${x(r.end_date)}</td>
                        <td class="text-center font-weight-bold text-dark py-3">${r.days}</td>
                        <td class="text-nowrap text-muted small font-weight-bold text-uppercase py-3">${x(r.submitted_at)}</td>
                        <td class="pe-4 py-3 text-center">
                            <button class="btn btn-sm btn-secondary font-weight-bold px-3 shadow-sm me-1" 
                                onclick="doApproveLeave(${r.id}, '${x(r.employee)}')">Approve</button>
                            <button class="btn btn-sm btn-outline-dark font-weight-bold px-3" 
                                onclick="openReject('leave', ${r.id}, '${x(r.employee)} – ${x(r.leave_type)} (${r.days} day(s))')">Reject</button>
                        </td>
                    </tr>`;
                }).join('');

                html += `</tbody></table></div>`;
                c.innerHTML = html;
            })
            .catch(function () { document.getElementById('leaveContainer').innerHTML = errorState(); });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOAD: OVERTIME
    // ─────────────────────────────────────────────────────────────────────────
    function loadOvertime() {
        const c = document.getElementById('overtimeContainer');
        c.innerHTML = spinner();

        fetch(BASE + '/overtime')
            .then(r => r.json())
            .then(function (data) {
                if (!data.length) { c.innerHTML = emptyState('No pending overtime requests.'); return; }

                let html = `
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="border-0 ps-4 py-3">Employee</th>
                                <th class="border-0 py-3">Date</th>
                                <th class="border-0 py-3">OT Type</th>
                                <th class="border-0 py-3 text-center">Hours</th>
                                <th class="border-0 py-3">Est. Pay</th>
                                <th class="border-0 py-3">Submitted</th>
                                <th class="border-0 py-3 pe-4 text-center" style="width:200px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                html += data.map(function (r) {
                    return `
                    <tr class="border-bottom bg-white">
                        <td class="ps-4 py-3">
                            <div class="font-weight-bold text-dark">${x(r.employee)}</div>
                            <div class="text-muted small font-weight-bold text-uppercase" style="font-size:0.7rem">${x(r.employee_id)}</div>
                        </td>
                        <td class="text-nowrap font-weight-bold text-secondary py-3">${x(r.date)}</td>
                        <td class="py-3">
                            <span class="badge bg-light border text-dark px-2 py-1 text-uppercase">${x(r.ot_type)}</span>
                        </td>
                        <td class="text-center font-weight-bold text-dark py-3">${r.hours} <span class="text-muted fw-normal small">h</span></td>
                        <td class="text-nowrap font-weight-bold text-dark py-3">₱${x(r.estimated_pay)}</td>
                        <td class="text-nowrap text-muted small font-weight-bold text-uppercase py-3">${x(r.submitted_at)}</td>
                        <td class="pe-4 py-3 text-center">
                            <button class="btn btn-sm btn-secondary font-weight-bold px-3 shadow-sm me-1" 
                                onclick="doApproveOT(${r.id}, '${x(r.employee)}')">Approve</button>
                            <button class="btn btn-sm btn-outline-dark font-weight-bold px-3" 
                                onclick="openReject('overtime', ${r.id}, '${x(r.employee)} – ${x(r.ot_type)} (${r.hours}h)')">Reject</button>
                        </td>
                    </tr>`;
                }).join('');

                html += `</tbody></table></div>`;
                c.innerHTML = html;
            })
            .catch(function () { document.getElementById('overtimeContainer').innerHTML = errorState(); });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOAD: PROFILE
    // ─────────────────────────────────────────────────────────────────────────
    function loadProfile() {
        const c = document.getElementById('profileContainer');
        c.innerHTML = spinner();

        fetch(BASE + '/profile')
            .then(r => r.json())
            .then(function (data) {
                if (!data.length) { c.innerHTML = emptyState('No pending profile update requests.'); return; }

                let html = `
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="border-0 ps-4 py-3">Employee</th>
                                <th class="border-0 py-3">Field</th>
                                <th class="border-0 py-3">Current Value</th>
                                <th class="border-0 py-3">Requested Value</th>
                                <th class="border-0 py-3">Submitted</th>
                                <th class="border-0 py-3 pe-4 text-center" style="width:200px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                html += data.map(function (r) {
                    return `
                    <tr class="border-bottom bg-white">
                        <td class="ps-4 py-3">
                            <div class="font-weight-bold text-dark">${x(r.employee)}</div>
                            <div class="text-muted small font-weight-bold text-uppercase" style="font-size:0.7rem">${x(r.employee_id)}</div>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-light border text-dark px-2 py-1 text-uppercase">${x(r.field)}</span>
                        </td>
                        <td class="text-muted small font-weight-bold text-uppercase py-3">${x(r.old_value)}</td>
                        <td class="font-weight-bold text-dark py-3">${x(r.new_value)}</td>
                        <td class="text-nowrap text-muted small font-weight-bold text-uppercase py-3">${x(r.submitted_at)}</td>
                        <td class="pe-4 py-3 text-center">
                            <button class="btn btn-sm btn-secondary font-weight-bold px-3 shadow-sm me-1" 
                                onclick="doApproveProfile('${r.id}', '${x(r.employee)}', '${x(r.field)}')">Approve</button>
                            <button class="btn btn-sm btn-outline-dark font-weight-bold px-3" 
                                onclick="openReject('profile', '${r.id}', '${x(r.employee)} – ${x(r.field)}')">Reject</button>
                        </td>
                    </tr>`;
                }).join('');

                html += `</tbody></table></div>`;
                c.innerHTML = html;
            })
            .catch(function () { document.getElementById('profileContainer').innerHTML = errorState(); });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOAD: HISTORY
    // ─────────────────────────────────────────────────────────────────────────
    window.loadHistory = function (type) {
        document.querySelectorAll('#historyFilters button').forEach(function (btn) {
            const active = btn.getAttribute('data-hfilter') === type;
            btn.className = active
                ? 'btn btn-secondary font-weight-bold px-4 shadow-sm'
                : 'btn btn-outline-dark font-weight-bold px-4';
        });

        const c = document.getElementById('historyContainer');
        c.innerHTML = spinner();

        fetch(BASE + '/history?type=' + type)
            .then(r => r.json())
            .then(function (data) {
                if (!data.length) { c.innerHTML = emptyState('No history records found.'); return; }

                let html = `
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-top">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="border-0 ps-4 py-3">Type</th>
                                <th class="border-0 py-3">Employee</th>
                                <th class="border-0 py-3">Detail</th>
                                <th class="border-0 py-3">Status</th>
                                <th class="border-0 py-3">Reviewed By</th>
                                <th class="border-0 pe-4 py-3">Reviewed At</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                html += data.map(function (r) {
                    const statusBadge = r.status === 'approved' || r.status === 'paid'
                        ? `<span class="badge bg-secondary px-3 py-2 text-uppercase">Approved</span>`
                        : `<span class="badge bg-light border text-muted px-3 py-2 text-uppercase">Rejected</span>`;

                    const rejNote = r.rejection_reason
                        ? `<div class="text-dark mt-2 pt-2 border-top font-weight-bold small">Reason: ${x(r.rejection_reason)}</div>`
                        : '';

                    return `
                    <tr class="border-bottom bg-white">
                        <td class="ps-4 py-3">
                            <span class="badge bg-light border text-dark px-2 py-1 text-uppercase">${x(r.category_label)}</span>
                        </td>
                        <td class="py-3">
                            <div class="font-weight-bold text-dark">${x(r.employee)}</div>
                            <div class="text-muted small font-weight-bold text-uppercase" style="font-size:0.7rem">${x(r.employee_id)}</div>
                        </td>
                        <td class="py-3">
                            <div class="font-weight-bold text-dark">${x(r.detail)}</div>
                            <div class="text-muted small font-weight-bold text-uppercase mt-1">${x(r.period)}</div>
                            ${rejNote}
                        </td>
                        <td class="py-3">${statusBadge}</td>
                        <td class="small font-weight-bold text-dark text-uppercase py-3">${x(r.reviewed_by)}</td>
                        <td class="text-nowrap small text-muted font-weight-bold text-uppercase pe-4 py-3">${x(r.reviewed_at)}</td>
                    </tr>`;
                }).join('');

                html += `</tbody></table></div>`;
                c.innerHTML = html;
            })
            .catch(function () { document.getElementById('historyContainer').innerHTML = errorState(); });
    };

    // ─────────────────────────────────────────────────────────────────────────
    // ACTIONS — LEAVE
    // ─────────────────────────────────────────────────────────────────────────
    window.doApproveLeave = function (id, employee) {
        Swal.fire({
            title: 'Approve leave request?',
            text: employee + '\'s leave will be validated and balance deducted.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Approve',
            confirmButtonColor: '#1a1a1a',
            cancelButtonColor: '#6c757d',
        }).then(function ({ isConfirmed }) {
            if (!isConfirmed) return;
            patchRequest('/leave/' + id + '/approve', {})
                .then(function (res) {
                    toast(res.message);
                    tabsLoaded.leave = false;
                    loadLeave();
                    loadCounts();
                })
                .catch(handleError);
        });
    };

    // ─────────────────────────────────────────────────────────────────────────
    // ACTIONS — OVERTIME
    // ─────────────────────────────────────────────────────────────────────────
    window.doApproveOT = function (id, employee) {
        Swal.fire({
            title: 'Approve overtime?',
            text: employee + '\'s overtime will be flagged for payroll inclusion.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Approve',
            confirmButtonColor: '#1a1a1a',
            cancelButtonColor: '#6c757d',
        }).then(function ({ isConfirmed }) {
            if (!isConfirmed) return;
            patchRequest('/overtime/' + id + '/approve', {})
                .then(function (res) {
                    toast(res.message);
                    tabsLoaded.overtime = false;
                    loadOvertime();
                    loadCounts();
                })
                .catch(handleError);
        });
    };

    // ─────────────────────────────────────────────────────────────────────────
    // ACTIONS — PROFILE
    // ─────────────────────────────────────────────────────────────────────────
    window.doApproveProfile = function (id, employee, field) {
        Swal.fire({
            title: 'Approve profile update?',
            text: 'The ' + field + ' field for ' + employee + ' will be updated immediately.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Apply',
            confirmButtonColor: '#1a1a1a',
            cancelButtonColor: '#6c757d',
        }).then(function ({ isConfirmed }) {
            if (!isConfirmed) return;
            patchRequest('/profile/' + id + '/approve', {})
                .then(function (res) {
                    toast(res.message);
                    tabsLoaded.profile = false;
                    loadProfile();
                    loadCounts();
                })
                .catch(handleError);
        });
    };

    // ─────────────────────────────────────────────────────────────────────────
    // SHARED REJECT MODAL
    // ─────────────────────────────────────────────────────────────────────────
    window.openReject = function (type, id, detail) {
        rejectState = { type: type, id: id };
        document.getElementById('rejectDetails').textContent = detail;
        document.getElementById('rejectReason').value = '';
        document.getElementById('rejectReason').classList.remove('is-invalid');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('rejectModal')).show();
    };

    function submitReject() {
        const reason = document.getElementById('rejectReason').value.trim();
        if (!reason) {
            document.getElementById('rejectReason').classList.add('is-invalid');
            return;
        }

        const { type, id } = rejectState;

        patchRequest('/' + type + '/' + id + '/reject', { reason: reason })
            .then(function (res) {
                bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
                toast(res.message);
                loadCounts();

                if (type === 'leave')    { tabsLoaded.leave    = false; loadLeave(); }
                if (type === 'overtime') { tabsLoaded.overtime = false; loadOvertime(); }
                if (type === 'profile')  { tabsLoaded.profile  = false; loadProfile(); }
            })
            .catch(handleError);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HTTP HELPERS
    // ─────────────────────────────────────────────────────────────────────────
    function patchRequest(path, body) {
        return fetch(BASE + path, {
            method: 'PATCH',
            headers: {
                'Content-Type':  'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  CSRF,
            },
            body: JSON.stringify(body),
        }).then(function (r) {
            return r.json().then(function (data) {
                if (!r.ok) return Promise.reject(data);
                return data;
            });
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UI HELPERS
    // ─────────────────────────────────────────────────────────────────────────
    function spinner() {
        return `<div class="text-center py-5 bg-white text-muted font-weight-bold">
            <div class="spinner-border spinner-border-sm me-2 text-secondary"></div>Loading Data…</div>`;
    }

    function emptyState(msg) {
        return `<div class="text-center py-5 bg-white text-muted font-weight-bold text-uppercase border-bottom">` + x(msg) + `</div>`;
    }

    function errorState() {
        return `<div class="text-center py-5 bg-white text-dark font-weight-bold">Failed to load data. Please refresh the page.</div>`;
    }

    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = (val !== undefined && val !== null) ? val : '—';
    }

    function toast(msg, icon) {
        Swal.fire({
            toast: true, position: 'top-end',
            icon: icon || 'success',
            title: msg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    }

    function handleError(err) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: err && err.message ? err.message : 'Something went wrong. Please try again.',
            confirmButtonColor: '#1a1a1a'
        });
    }

    function x(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

})();
</script>
@endpush