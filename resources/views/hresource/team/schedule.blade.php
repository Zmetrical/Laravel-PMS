@extends('layouts.main')

@section('title', 'Team Schedule')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">Team Schedule</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- ── Section 1: Templates ──────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Schedule Templates</h6>
        <button class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm" id="btn-add-template">
            New Template
        </button>
    </div>
    <div class="card-body p-4 bg-light">
        <div class="row g-4" id="template-cards">
            <div class="col-12 text-center py-5 text-muted font-weight-bold" id="template-loading">
                <div class="spinner-border spinner-border-sm me-2 text-secondary"></div> Loading Templates…
            </div>
        </div>
    </div>
</div>

{{-- ── Section 2: Assignments ────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Employee Assignments</h6>
        <button class="btn btn-outline-dark font-weight-bold px-4 py-2" id="btn-assign">
            Assign Schedule
        </button>
    </div>

    {{-- Filters --}}
    <div class="card-body bg-light border-bottom p-4">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 font-weight-bold text-dark ps-0" id="assign-search"
                           placeholder="Search by name, ID, dept…">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select shadow-sm font-weight-bold text-dark" id="assign-dept">
                    <option value="">All Departments</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select shadow-sm font-weight-bold text-dark" id="assign-template-filter">
                    <option value="">All Schedules</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Assignment Table --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 ps-3 py-3" style="width:60px"></th>
                        <th class="border-0 font-weight-bold py-3">Employee</th>
                        <th class="border-0 font-weight-bold py-3">Department / Position</th>
                        <th class="border-0 font-weight-bold py-3">Current Schedule</th>
                        <th class="border-0 font-weight-bold py-3">Effective Date</th>
                        <th class="border-0 font-weight-bold py-3 text-center pe-3" style="width:100px">Action</th>
                    </tr>
                </thead>
                <tbody id="assign-tbody">
                    <tr>
                        <td colspan="6" class="text-center py-5 bg-white text-muted font-weight-bold">
                            <div class="spinner-border spinner-border-sm me-2 text-secondary"></div> Loading Assignments…
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-light py-3 border-top">
        <small class="text-muted font-weight-bold text-uppercase" id="assign-footer">—</small>
    </div>
</div>

{{-- ── Template Modal (Add / Edit) ──────────────────────────── --}}
<div class="modal fade" id="template-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase" id="template-modal-title">New Template</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="t-id">
                
                <div class="bg-white border rounded p-4 shadow-sm mb-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                                Template Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control border-secondary font-weight-bold text-dark shadow-sm" id="t-name"
                                   placeholder="e.g. Morning Shift">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Grace Period (min)</label>
                            <input type="number" class="form-control shadow-sm font-weight-bold text-dark" id="t-grace"
                                   min="0" max="60" value="0">
                        </div>
                        <div class="col-md-2 d-flex align-items-end pb-2">
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" class="form-check-input" id="t-active" checked style="cursor:pointer;">
                                <label class="form-check-label small font-weight-bold text-uppercase ms-1 text-dark" for="t-active" style="cursor:pointer;">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Day Rows --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small font-weight-bold text-uppercase">Work Days & Shifts</span>
                </div>
                
                <div class="bg-white border rounded shadow-sm overflow-hidden">
                    <div class="table-responsive mb-0">
                        <table class="table table-hover align-middle mb-0" id="days-table">
                            <thead class="bg-light text-muted small text-uppercase border-bottom">
                                <tr>
                                    <th class="border-0 ps-3 py-3" style="width:80px">Day</th>
                                    <th class="border-0 py-3">Full Name</th>
                                    <th class="border-0 py-3 text-center">Working Day</th>
                                    <th class="border-0 py-3">Shift In</th>
                                    <th class="border-0 py-3 pe-3">Shift Out</th>
                                </tr>
                            </thead>
                            <tbody id="days-tbody">
                                {{-- Rendered by JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white py-3">
                <button class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-secondary font-weight-bold px-4" id="btn-save-template">
                    Save Template
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Assign Modal ──────────────────────────────────────────── --}}
<div class="modal fade" id="assign-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase">Assign Schedule</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                
                {{-- Step 1: pick template + effective date --}}
                <div class="bg-white border rounded p-4 shadow-sm mb-4">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                                Schedule Template <span class="text-danger">*</span>
                            </label>
                            <select class="form-select border-secondary shadow-sm font-weight-bold text-dark" id="a-template-id">
                                <option value="">— Select Template —</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                                Effective Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control shadow-sm font-weight-bold text-dark" id="a-effective-date"
                                   value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                {{-- Step 2: select employees --}}
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                    <span class="text-muted small font-weight-bold text-uppercase">Select Employees</span>
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <select class="form-select shadow-sm font-weight-bold text-dark" id="a-emp-dept" style="width:180px;">
                            <option value="">All Departments</option>
                        </select>
                        <div class="input-group shadow-sm" style="width:220px">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 font-weight-bold text-dark ps-0" id="a-emp-search"
                                   placeholder="Search…">
                        </div>
                        <div class="form-check mb-0 ms-2">
                            <input type="checkbox" class="form-check-input border-secondary" id="a-select-all" style="cursor:pointer;">
                            <label class="form-check-label small font-weight-bold text-uppercase text-dark ms-1" for="a-select-all" style="cursor:pointer;">Select All</label>
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded shadow-sm overflow-hidden">
                    <div style="max-height:350px;overflow-y:auto;">
                        <table class="table table-hover align-middle mb-0">
                            <tbody id="a-emp-tbody">
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted font-weight-bold">
                                        <div class="spinner-border spinner-border-sm me-2 text-secondary"></div>Loading Employees…
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mt-3 text-end">
                    <span class="badge bg-secondary px-3 py-2 text-uppercase font-weight-bold shadow-sm" id="a-selected-count">0 Selected</span>
                </div>

            </div>
            <div class="modal-footer bg-white py-3">
                <button class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-secondary font-weight-bold px-4" id="btn-save-assign">
                    Assign Employees
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const TeamSchedule = (() => {

    /* ─── Routes ────────────────────────────────────────────── */
    const ROUTES = {
        templates:   '{{ route('hresource.team_schedule.templates') }}',
        storeT:      '{{ route('hresource.team_schedule.templates.store') }}',
        updateT:     '{{ url('hresource/team-schedule/templates') }}',
        destroyT:    '{{ url('hresource/team-schedule/templates') }}',
        assignments: '{{ route('hresource.team_schedule.assignments') }}',
        assign:      '{{ route('hresource.team_schedule.assign') }}',
    };
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const DAY_NAMES = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const DAY_SHORT = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

    /* ─── API ───────────────────────────────────────────────── */
    async function api(url, options = {}) {
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

    /* ─── Lazy modals ───────────────────────────────────────── */
    let _tplModal    = null;
    let _assignModal = null;
    const getTplModal    = () => _tplModal    ??= bootstrap.Modal.getOrCreateInstance(document.getElementById('template-modal'));
    const getAssignModal = () => _assignModal ??= bootstrap.Modal.getOrCreateInstance(document.getElementById('assign-modal'));

    /* ─── State ─────────────────────────────────────────────── */
    let templates    = [];
    let allEmployees = [];
    let filtered     = [];
    let editingTplId = null;
    let selectedEmpIds = new Set();

    /* ─── DOM ───────────────────────────────────────────────── */
    const $ = id => document.getElementById(id);

    /* ═══════════════════════════════════════════════════════════
       SECTION 1 — TEMPLATES
    ═══════════════════════════════════════════════════════════ */

    async function loadTemplates() {
        try {
            templates = await api(ROUTES.templates);
            renderTemplateCards();
            populateTemplateFilters();
        } catch (e) {
            $('template-cards').innerHTML =
                `<div class="col-12 text-center text-dark font-weight-bold py-5 bg-white border rounded">Error: ${e.message}</div>`;
        }
    }

    function renderTemplateCards() {
        const container = $('template-cards');

        if (!templates.length) {
            container.innerHTML = `<div class="col-12 text-center text-muted font-weight-bold py-5 bg-white border rounded shadow-sm">
                No templates yet. Create one to get started.</div>`;
            return;
        }

        container.innerHTML = templates.map(tpl => {
            const workDays = tpl.days.filter(d => d.is_working_day);
            const dayBadges = tpl.days.map(d =>
                `<span class="badge ${d.is_working_day ? 'bg-secondary text-white' : 'bg-light border text-muted'} px-2 py-1 mb-1 me-1 text-uppercase" style="font-size: 0.65rem;">
                    ${DAY_SHORT[d.day_of_week]}
                </span>`
            ).join('');

            const first = workDays[0];
            const shiftLabel = first?.shift_in
                ? `${first.shift_in.slice(0,5)} – ${(first.shift_out ?? '').slice(0,5)}`
                : '—';

            return `
            <div class="col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100 ${tpl.is_active ? 'bg-white' : 'bg-light opacity-75'}">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="pe-3">
                                <div class="font-weight-bold text-dark text-uppercase mb-1 lh-sm" style="font-size: 1.05rem;">${tpl.name}</div>
                                <div class="text-secondary small font-weight-bold">${shiftLabel}</div>
                            </div>
                            <div class="d-flex gap-2 flex-shrink-0">
                                <button class="btn btn-sm btn-light border text-dark btn-edit-tpl px-2"
                                        data-id="${tpl.id}" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button class="btn btn-sm btn-light border text-danger btn-del-tpl px-2"
                                        data-id="${tpl.id}" data-name="${tpl.name}" title="Delete">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap mb-3 border-top pt-3">${dayBadges}</div>

                        <div class="d-flex justify-content-between text-muted small font-weight-bold text-uppercase border-top pt-3" style="font-size: 0.7rem;">
                            <span><span class="text-dark">${workDays.length}</span> Working Days</span>
                            <span><span class="text-dark">${tpl.employee_count ?? 0}</span> Assigned</span>
                        </div>
                        ${tpl.grace_period_minutes > 0
                            ? `<div class="text-secondary small font-weight-bold text-uppercase mt-2" style="font-size: 0.7rem;">Grace Period: ${tpl.grace_period_minutes} min</div>`
                            : ''}
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    function populateTemplateFilters() {
        const sel = $('assign-template-filter');
        const asel = $('a-template-id');

        const opts = templates.map(t =>
            `<option value="${t.id}">${t.name}</option>`
        ).join('');

        sel.innerHTML  = '<option value="">All Schedules</option>' + opts;
        asel.innerHTML = '<option value="">— Select Template —</option>' + opts;
    }

    /* ─── Template Modal ────────────────────────────────────── */
    function buildDayRows(existing = []) {
        const tbody = $('days-tbody');
        tbody.innerHTML = Array.from({ length: 7 }, (_, i) => {
            const ex = existing.find(d => d.day_of_week === i) ?? {};
            const isWorking = ex.is_working_day ?? (i >= 1 && i <= 6);
            return `
            <tr class="border-bottom" data-dow="${i}">
                <td class="text-center ps-3 py-3">
                    <span class="badge ${isWorking ? 'bg-secondary text-white' : 'bg-light border text-muted'} px-2 py-1 text-uppercase">${DAY_SHORT[i]}</span>
                </td>
                <td class="text-dark font-weight-bold py-3">${DAY_NAMES[i]}</td>
                <td class="text-center py-3">
                    <input type="checkbox" class="form-check-input day-working border-secondary" style="cursor:pointer;" ${isWorking ? 'checked' : ''}>
                </td>
                <td class="py-3">
                    <input type="time" class="form-control shadow-sm font-weight-bold text-dark day-in"
                           value="${ex.shift_in ? ex.shift_in.slice(0,5) : ''}"
                           ${!isWorking ? 'disabled' : ''}>
                </td>
                <td class="py-3 pe-3">
                    <input type="time" class="form-control shadow-sm font-weight-bold text-dark day-out"
                           value="${ex.shift_out ? ex.shift_out.slice(0,5) : ''}"
                           ${!isWorking ? 'disabled' : ''}>
                </td>
            </tr>`;
        }).join('');

        tbody.querySelectorAll('.day-working').forEach(chk => {
            chk.addEventListener('change', () => {
                const row = chk.closest('tr');
                const badge = row.querySelector('.badge');
                
                row.querySelector('.day-in').disabled  = !chk.checked;
                row.querySelector('.day-out').disabled = !chk.checked;
                
                if (chk.checked) {
                    badge.className = 'badge bg-secondary text-white px-2 py-1 text-uppercase';
                } else {
                    badge.className = 'badge bg-light border text-muted px-2 py-1 text-uppercase';
                    row.querySelector('.day-in').value  = '';
                    row.querySelector('.day-out').value = '';
                }
            });
        });
    }

    function openAddTemplate() {
        editingTplId = null;
        $('template-modal-title').textContent = 'New Template';
        $('t-id').value    = '';
        $('t-name').value  = '';
        $('t-grace').value = '0';
        $('t-active').checked = true;
        buildDayRows();
        getTplModal().show();
    }

    function openEditTemplate(id) {
        const tpl = templates.find(t => t.id == id);
        if (!tpl) return;

        editingTplId = id;
        $('template-modal-title').textContent = 'Edit Template';
        $('t-id').value    = tpl.id;
        $('t-name').value  = tpl.name;
        $('t-grace').value = tpl.grace_period_minutes;
        $('t-active').checked = tpl.is_active;
        buildDayRows(tpl.days);
        getTplModal().show();
    }

    function collectDayRows() {
        return Array.from($('days-tbody').querySelectorAll('tr')).map(row => ({
            day_of_week:    parseInt(row.dataset.dow),
            is_working_day: row.querySelector('.day-working').checked,
            shift_in:       row.querySelector('.day-in').value  || null,
            shift_out:      row.querySelector('.day-out').value || null,
        }));
    }

    async function saveTemplate() {
        const btn  = $('btn-save-template');
        const name = $('t-name').value.trim();

        if (!name) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Template name is required.', confirmButtonColor: '#1a1a1a' });
            return;
        }

        const payload = {
            name:                 name,
            grace_period_minutes: parseInt($('t-grace').value) || 0,
            is_active:            $('t-active').checked,
            days:                 collectDayRows(),
        };

        btn.disabled = true;
        btn.innerHTML = 'Saving…';

        try {
            const isEdit = !!editingTplId;
            const url    = isEdit ? `${ROUTES.updateT}/${editingTplId}` : ROUTES.storeT;
            const method = isEdit ? 'PATCH' : 'POST';

            await api(url, { method, body: JSON.stringify(payload) });

            Swal.fire({ icon: 'success', title: isEdit ? 'Updated!' : 'Created!',
                timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });

            getTplModal().hide();
            await loadTemplates();

        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#1a1a1a' });
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Save Template';
        }
    }

    async function deleteTemplate(id, name) {
        const confirm = await Swal.fire({
            title: `Delete "${name}"?`,
            text:  'This cannot be undone.',
            icon:  'warning',
            showCancelButton:   true,
            confirmButtonColor: '#1a1a1a',
            cancelButtonColor:  '#6c757d',
            confirmButtonText:  'Yes, Delete',
        });
        if (!confirm.isConfirmed) return;

        try {
            await api(`${ROUTES.destroyT}/${id}`, { method: 'DELETE' });
            Swal.fire({ icon: 'success', title: 'Deleted',
                timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
            await loadTemplates();
            await loadAssignments();
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Cannot Delete', text: e.message, confirmButtonColor: '#1a1a1a' });
        }
    }

    /* ═══════════════════════════════════════════════════════════
       SECTION 2 — ASSIGNMENTS
    ═══════════════════════════════════════════════════════════ */

    async function loadAssignments() {
        const tbody = $('assign-tbody');
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 bg-white text-muted font-weight-bold">
            <div class="spinner-border spinner-border-sm me-2 text-secondary"></div>Loading Assignments…</td></tr>`;

        try {
            allEmployees = await api(ROUTES.assignments);
            filtered     = [...allEmployees];
            populateDeptFilter();
            renderAssignments();
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 bg-white text-dark font-weight-bold">Error: ${e.message}</td></tr>`;
        }
    }

    function populateDeptFilter() {
        const depts = [...new Set(allEmployees.map(e => e.department).filter(Boolean))].sort();
        const opts = '<option value="">All Departments</option>' + depts.map(d => `<option value="${d}">${d}</option>`).join('');
        
        $('assign-dept').innerHTML = opts;
        $('a-emp-dept').innerHTML  = opts;
    }

    function renderAssignments() {
        const tbody = $('assign-tbody');

        if (!filtered.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 bg-white text-muted font-weight-bold">No employees found.</td></tr>`;
            $('assign-footer').textContent = 'No results';
            return;
        }

        tbody.innerHTML = filtered.map(emp => {
            const schedule = emp.current_schedule;
            const tplName  = schedule?.template?.name ?? '—';
            const effDate  = schedule?.effective_date  ?? '—';
            const initials = emp.fullName.split(' ').map(w => w[0]).join('').slice(0,2).toUpperCase();

            return `<tr class="border-bottom bg-white">
                <td class="text-center ps-3 py-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white font-weight-bold shadow-sm" style="width:36px;height:36px;font-size:.8rem">
                        ${initials}
                    </span>
                </td>
                <td class="py-3">
                    <div class="font-weight-bold text-dark">${emp.fullName}</div>
                    <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 0.7rem;">${emp.id}</div>
                </td>
                <td class="py-3">
                    <div class="font-weight-bold text-dark">${emp.department ?? '—'}</div>
                    <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 0.7rem;">${emp.position ?? '—'}</div>
                </td>
                <td class="py-3">
                    ${schedule
                        ? `<span class="badge bg-light border text-dark px-2 py-1">${tplName}</span>`
                        : `<span class="badge bg-light border border-secondary text-muted px-2 py-1">No Schedule</span>`}
                </td>
                <td class="text-muted small font-weight-bold text-uppercase py-3">${effDate}</td>
                <td class="py-3 text-center pe-3">
                    <button class="btn btn-sm btn-light border text-dark font-weight-bold px-3 btn-quick-assign shadow-sm"
                            data-id="${emp.id}" data-name="${emp.fullName}" title="Assign Schedule">
                        Assign
                    </button>
                </td>
            </tr>`;
        }).join('');

        $('assign-footer').textContent = `Showing ${filtered.length} of ${allEmployees.length} employee(s)`;
    }

    function applyAssignFilters() {
        const q    = $('assign-search').value.toLowerCase();
        const dept = $('assign-dept').value;
        const tpl  = $('assign-template-filter').value;

        filtered = allEmployees.filter(e => {
            const matchQ    = !q   || [e.fullName, e.id, e.department]
                                .some(v => (v ?? '').toLowerCase().includes(q));
            const matchDept = !dept || e.department === dept;
            const matchTpl  = !tpl  ||
                String(e.current_schedule?.template_id) === String(tpl);
            return matchQ && matchDept && matchTpl;
        });

        renderAssignments();
    }

    /* ─── Assign Modal ──────────────────────────────────────── */
    function openAssignModal(preselectedId = null) {
        selectedEmpIds.clear();
        $('a-select-all').checked = false;
        $('a-emp-search').value   = '';
        $('a-emp-dept').value     = '';

        if (preselectedId) {
            selectedEmpIds.add(String(preselectedId));
        }

        renderAssignEmployeeList();
        getAssignModal().show();
    }

    function renderAssignEmployeeList() {
        const tbody = $('a-emp-tbody');
        const q     = $('a-emp-search').value.toLowerCase();
        const dept  = $('a-emp-dept').value;

        const list = allEmployees.filter(e => {
            const matchQ    = !q || [e.fullName, e.id, e.department].some(v => (v ?? '').toLowerCase().includes(q));
            const matchDept = !dept || e.department === dept;
            return matchQ && matchDept;
        });

        if (!list.length) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-muted font-weight-bold">No employees found.</td></tr>`;
            return;
        }

        tbody.innerHTML = list.map(emp => `
            <tr class="border-bottom">
                <td class="ps-3 py-3" style="width:48px">
                    <input type="checkbox" class="form-check-input a-emp-chk border-secondary" style="cursor:pointer;"
                           value="${emp.id}" ${selectedEmpIds.has(String(emp.id)) ? 'checked' : ''}>
                </td>
                <td class="py-3">
                    <div class="font-weight-bold text-dark">${emp.fullName}</div>
                    <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 0.7rem;">${emp.id}</div>
                </td>
                <td class="text-muted small font-weight-bold text-uppercase pe-3 py-3 text-end">${emp.department ?? '—'}</td>
            </tr>`).join('');

        tbody.querySelectorAll('.a-emp-chk').forEach(chk => {
            chk.addEventListener('change', () => {
                chk.checked
                    ? selectedEmpIds.add(chk.value)
                    : selectedEmpIds.delete(chk.value);
                updateSelectedCount();
            });
        });

        updateSelectedCount();
    }

    function updateSelectedCount() {
        $('a-selected-count').textContent = `${selectedEmpIds.size} Selected`;
    }

    async function saveAssignment() {
        const btn        = $('btn-save-assign');
        const templateId = $('a-template-id').value;
        const effDate    = $('a-effective-date').value;

        if (!templateId) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Please select a schedule template.', confirmButtonColor: '#1a1a1a' });
            return;
        }
        if (!selectedEmpIds.size) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Select at least one employee.', confirmButtonColor: '#1a1a1a' });
            return;
        }

        btn.disabled = true;
        btn.innerHTML = 'Assigning…';

        try {
            const data = await api(ROUTES.assign, {
                method: 'POST',
                body:   JSON.stringify({
                    user_ids:       [...selectedEmpIds],
                    template_id:    templateId,
                    effective_date: effDate,
                }),
            });

            Swal.fire({ icon: 'success', title: 'Assigned!', text: data.message, timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });

            getAssignModal().hide();
            await loadAssignments();
            await loadTemplates(); 

        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#1a1a1a' });
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Assign Employees';
        }
    }

    /* ─── Event Binding ─────────────────────────────────────── */
    function bind() {
        // Template section
        $('btn-add-template').addEventListener('click', openAddTemplate);
        $('btn-save-template').addEventListener('click', saveTemplate);

        $('template-cards').addEventListener('click', e => {
            const editBtn = e.target.closest('.btn-edit-tpl');
            const delBtn  = e.target.closest('.btn-del-tpl');
            if (editBtn) openEditTemplate(editBtn.dataset.id);
            if (delBtn)  deleteTemplate(delBtn.dataset.id, delBtn.dataset.name);
        });

        // Assignment section filters
        let t;
        $('assign-search').addEventListener('input', () => {
            clearTimeout(t); t = setTimeout(applyAssignFilters, 300);
        });
        $('assign-dept').addEventListener('change',            applyAssignFilters);
        $('assign-template-filter').addEventListener('change', applyAssignFilters);

        // Assign modal
        $('btn-assign').addEventListener('click', () => openAssignModal());
        $('btn-save-assign').addEventListener('click', saveAssignment);

        $('assign-tbody').addEventListener('click', e => {
            const btn = e.target.closest('.btn-quick-assign');
            if (btn) openAssignModal(btn.dataset.id);
        });

        $('a-select-all').addEventListener('change', () => {
            document.querySelectorAll('.a-emp-chk').forEach(chk => {
                chk.checked = $('a-select-all').checked;
                $('a-select-all').checked
                    ? selectedEmpIds.add(chk.value)
                    : selectedEmpIds.delete(chk.value);
            });
            updateSelectedCount();
        });

        $('a-emp-search').addEventListener('input', renderAssignEmployeeList);
        $('a-emp-dept').addEventListener('change', renderAssignEmployeeList);
    }

    /* ─── Init ──────────────────────────────────────────────── */
    async function init() {
        bind();
        await Promise.all([loadTemplates(), loadAssignments()]);
    }

    return { init };
})();

document.addEventListener('DOMContentLoaded', () => TeamSchedule.init());
</script>
@endpush