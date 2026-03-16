@extends('layouts.main')

@section('title', 'Team Attendance')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">Team Attendance</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- ── View Toggle + Header ──────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="d-flex gap-2">
        <button class="btn btn-secondary font-weight-bold px-4 shadow-sm" id="btn-view-daily">Daily View</button>
        <button class="btn btn-outline-dark font-weight-bold px-4" id="btn-view-employee">Employee View</button>
    </div>
    <button class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm" id="btn-add-record">Add Record</button>
</div>

{{-- ── Stats ─────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="border border-secondary rounded bg-light p-3 shadow-sm text-center h-100 d-flex flex-column justify-content-center stat-card" data-filter="all" style="cursor:pointer;">
            <span class="text-muted small font-weight-bold text-uppercase mb-1">Total</span>
            <span class="h3 font-weight-bold text-dark mb-0" id="stat-total">—</span>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="border rounded bg-white p-3 shadow-sm text-center h-100 d-flex flex-column justify-content-center stat-card" data-filter="present" style="cursor:pointer;">
            <span class="text-muted small font-weight-bold text-uppercase mb-1">Present</span>
            <span class="h3 font-weight-bold text-dark mb-0" id="stat-present">—</span>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="border rounded bg-white p-3 shadow-sm text-center h-100 d-flex flex-column justify-content-center stat-card" data-filter="late" style="cursor:pointer;">
            <span class="text-muted small font-weight-bold text-uppercase mb-1">Late</span>
            <span class="h3 font-weight-bold text-dark mb-0" id="stat-late">—</span>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="border rounded bg-white p-3 shadow-sm text-center h-100 d-flex flex-column justify-content-center stat-card" data-filter="absent" style="cursor:pointer;">
            <span class="text-muted small font-weight-bold text-uppercase mb-1">Absent</span>
            <span class="h3 font-weight-bold text-dark mb-0" id="stat-absent">—</span>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="border rounded bg-white p-3 shadow-sm text-center h-100 d-flex flex-column justify-content-center stat-card" data-filter="issues" style="cursor:pointer;">
            <span class="text-muted small font-weight-bold text-uppercase mb-1">Issues</span>
            <span class="h3 font-weight-bold text-dark mb-0" id="stat-issues">—</span>
        </div>
    </div>
</div>

{{-- ── Filters Card ──────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Filters</h6>
    </div>
    <div class="card-body p-4">

        {{-- Daily View Filters --}}
        <div id="filters-daily">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Date</label>
                    <input type="date" class="form-control shadow-sm" id="f-date" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Department</label>
                    <select class="form-control shadow-sm" id="f-department">
                        <option value="">All Departments</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Status</label>
                    <select class="form-control shadow-sm" id="f-status">
                        <option value="all">All Status</option>
                        <option value="present">Present</option>
                        <option value="late">Late</option>
                        <option value="absent">Absent</option>
                        <option value="incomplete">Incomplete</option>
                        <option value="issues">With Issues</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-secondary font-weight-bold shadow-sm w-100 py-2" id="btn-load-daily">Load Records</button>
                </div>
            </div>
        </div>

        {{-- Employee View Filters --}}
        <div id="filters-employee" class="d-none">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Employee</label>
                    {{--
                        Pattern: hidden input holds the resolved ID,
                        visible text input + datalist handles search.
                        Option format: "Full Name (EMP-001)" — browser searches both.
                    --}}
                    <input type="hidden" id="f-employee-id">
                    <input type="text"
                           class="form-control shadow-sm"
                           id="f-employee-search"
                           list="f-employee-list"
                           placeholder="Search by name or code…"
                           autocomplete="off">
                    <datalist id="f-employee-list"></datalist>
                    <small class="text-muted d-none" id="f-employee-hint"></small>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Month</label>
                    <select class="form-control shadow-sm" id="f-month">
                        @foreach(['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'] as $v => $l)
                            <option value="{{ $v }}" {{ $v == date('m') ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Year</label>
                    <select class="form-control shadow-sm" id="f-year">
                        @for($y = date('Y') - 2; $y <= date('Y'); $y++)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-secondary font-weight-bold shadow-sm w-100 py-2" id="btn-load-employee">Load Records</button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── Records Table ─────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase" id="records-title">Attendance Records</h6>
        <span class="badge bg-light border text-dark px-2 py-1" id="records-count">—</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 ps-3 py-3" style="width:48px"></th>
                        <th class="border-0 font-weight-bold py-3">Employee</th>
                        <th class="border-0 font-weight-bold py-3">Time In</th>
                        <th class="border-0 font-weight-bold py-3">Time Out</th>
                        <th class="border-0 font-weight-bold py-3">Hours</th>
                        <th class="border-0 font-weight-bold py-3">Late</th>
                        <th class="border-0 font-weight-bold py-3">Undertime</th>
                        <th class="border-0 font-weight-bold py-3">Status</th>
                        <th class="border-0 font-weight-bold py-3 text-center pe-3" style="width:80px">Actions</th>
                    </tr>
                </thead>
                <tbody id="attendance-tbody">
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted bg-white">
                            <span class="font-weight-bold d-block">Select a date and click Load to view records.</span>
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

{{-- ── Add / Edit Record Modal ───────────────────────────────── --}}
<div class="modal fade" id="record-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase" id="record-modal-title">Add Attendance Record</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="r-record-id">
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                            Employee <span class="text-danger">*</span>
                        </label>
                        <input type="hidden" id="r-user-id">
                        <input type="text"
                               class="form-control shadow-sm"
                               id="r-employee-search"
                               list="r-employee-list"
                               placeholder="Search by name or code…"
                               autocomplete="off">
                        <datalist id="r-employee-list"></datalist>
                        <small class="text-muted d-none" id="r-employee-hint"></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">
                            Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control shadow-sm font-weight-bold text-dark" id="r-date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Override Status</label>
                        <select class="form-control shadow-sm" id="r-status">
                            <option value="">Auto-compute</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                            <option value="leave">On Leave</option>
                            <option value="holiday">Holiday</option>
                            <option value="rest_day">Rest Day</option>
                            <option value="incomplete">Incomplete</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Time In</label>
                        <input type="time" class="form-control shadow-sm font-weight-bold text-dark" id="r-time-in">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Time Out</label>
                        <input type="time" class="form-control shadow-sm font-weight-bold text-dark" id="r-time-out">
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Notes</label>
                        <textarea class="form-control shadow-sm p-3" id="r-notes" rows="2" placeholder="Optional notes…"></textarea>
                    </div>
                </div>

                {{-- Computed preview --}}
                <div id="r-preview" class="mt-4 p-3 border border-secondary rounded bg-white shadow-sm d-none">
                    <p class="text-dark small font-weight-bold text-uppercase border-bottom pb-2 mb-3">Computed Values</p>
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size:0.65rem;">Hours Worked</div>
                            <span class="font-weight-bold text-dark" id="r-prev-hours">—</span>
                        </div>
                        <div class="col-4 border-start border-end">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size:0.65rem;">Late (min)</div>
                            <span class="font-weight-bold text-dark" id="r-prev-late">—</span>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small font-weight-bold text-uppercase mb-1" style="font-size:0.65rem;">Undertime</div>
                            <span class="font-weight-bold text-dark" id="r-prev-ut">—</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white py-3">
                <button class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-secondary font-weight-bold px-4" id="btn-save-record">Save Record</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const TeamAttendance = (() => {

    /* ─── Routes ────────────────────────────────────────────── */
    const ROUTES = {
        employees: '{{ route('hresource.team_attendance.employees') }}',
        records:   '{{ route('hresource.team_attendance.records') }}',
        upsert:    '{{ route('hresource.team_attendance.upsert') }}',
    };
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    /* ─── State ─────────────────────────────────────────────── */
    let view         = 'daily';
    let records      = [];
    let filtered     = [];
    let employees    = [];
    let activeFilter = 'all';

    /* ─── DOM helper (pure vanilla — no jQuery) ─────────────── */
    const el    = id => document.getElementById(id);
    const tbody = el('attendance-tbody');

    /* ─── API ───────────────────────────────────────────────── */
    async function api(url, options = {}) {
        const res = await fetch(url, {
            headers: {
                'Accept':       'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            ...options,
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message ?? `HTTP ${res.status}`);
        }
        return res.json();
    }

    /* ─── Bootstrap modal (lazy) ────────────────────────────── */
    let _bsModal = null;
    const getBsModal = () => _bsModal ??= new bootstrap.Modal(el('record-modal'));

    /* ──────────────────────────────────────────────────────────
     * EMPLOYEE SEARCH — native <datalist>, zero dependencies
     *
     * Each <option value> is stored as "Full Name (EMP-001)".
     * The browser's built-in datalist filter already searches
     * that string, so typing either a name fragment or a code
     * fragment works out of the box.
     *
     * resolveEmployeeId() extracts the ID from the chosen text
     * and writes it into the companion hidden input.
     * ────────────────────────────────────────────────────────── */

    function buildDatalistOptions() {
        const html = employees
            .map(e => `<option value="${e.fullName} (${e.id})">`)
            .join('');
        el('f-employee-list').innerHTML = html;
        el('r-employee-list').innerHTML = html;
    }

    /**
     * Parse "Full Name (EMP-001)" back to an employee ID.
     * Falls back to a loose name search if the parenthesis
     * pattern is not found (user typed a partial name and
     * did not pick from the list yet).
     */
    function resolveEmployeeId(text) {
        if (!text) return null;

        // Exact match from datalist option: "Name (ID)"
        const match = text.match(/\(([^)]+)\)\s*$/);
        if (match) {
            const found = employees.find(e => e.id === match[1]);
            if (found) return found.id;
        }

        // Loose name match (partial typing)
        const lower = text.toLowerCase();
        const found = employees.find(e =>
            e.fullName.toLowerCase().includes(lower) ||
            e.id.toLowerCase().includes(lower)
        );
        return found ? found.id : null;
    }

    function updateHint(hintId, employeeId) {
        const hint = el(hintId);
        const emp  = employees.find(e => e.id === employeeId);
        if (emp && (emp.department || emp.position)) {
            hint.textContent = [emp.department, emp.position].filter(Boolean).join(' · ');
            hint.classList.remove('d-none');
        } else {
            hint.textContent = '';
            hint.classList.add('d-none');
        }
    }

    function bindEmployeeSearch(searchId, hiddenId, hintId) {
        const input = el(searchId);

        const resolve = () => {
            const id = resolveEmployeeId(input.value.trim());
            el(hiddenId).value = id ?? '';
            updateHint(hintId, id);
        };

        input.addEventListener('input',  resolve);
        input.addEventListener('change', resolve);   // fires when datalist option is clicked
    }

    /** Programmatically set the search field (for openEdit) */
    function setEmployeeSearch(searchId, hiddenId, hintId, employeeId) {
        const emp = employees.find(e => e.id === employeeId);
        if (emp) {
            el(searchId).value = `${emp.fullName} (${emp.id})`;
            el(hiddenId).value = emp.id;
            updateHint(hintId, emp.id);
        } else {
            el(searchId).value = '';
            el(hiddenId).value = '';
            updateHint(hintId, null);
        }
    }

    function clearEmployeeSearch(searchId, hiddenId, hintId) {
        el(searchId).value = '';
        el(hiddenId).value = '';
        updateHint(hintId, null);
    }

    /* ─── Load employees once ───────────────────────────────── */
    async function loadEmployees() {
        try {
            employees = await api(ROUTES.employees);
            buildDatalistOptions();
            populateDeptFilter();
            bindEmployeeSearch('f-employee-search', 'f-employee-id', 'f-employee-hint');
            bindEmployeeSearch('r-employee-search', 'r-user-id',     'r-employee-hint');
        } catch (e) {
            console.error('Failed to load employees', e);
        }
    }

    function populateDeptFilter() {
        const depts = [...new Set(employees.map(e => e.department).filter(Boolean))].sort();
        el('f-department').innerHTML = '<option value="">All Departments</option>'
            + depts.map(d => `<option value="${d}">${d}</option>`).join('');
    }

    /* ─── Load Records ──────────────────────────────────────── */
    async function loadRecords() {
        tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 bg-white text-muted">
            <div class="spinner-border spinner-border-sm me-2"></div>
            <span class="font-weight-bold">Loading…</span>
        </td></tr>`;

        const params = new URLSearchParams();

        if (view === 'daily') {
            params.set('date', el('f-date').value);
            if (el('f-department').value) params.set('department', el('f-department').value);
        } else {
            const empId = el('f-employee-id').value;
            if (!empId) {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 bg-white text-muted font-weight-bold">
                    Select an employee to view records.</td></tr>`;
                return;
            }
            params.set('user_id', empId);
            params.set('month',   parseInt(el('f-month').value));
            params.set('year',    el('f-year').value);
        }

        const status = el('f-status')?.value ?? 'all';
        if (status && status !== 'all') params.set('status', status);

        try {
            records      = await api(`${ROUTES.records}?${params}`);
            filtered     = [...records];
            activeFilter = 'all';
            updateTitle();
            updateStats();
            renderTable();
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 bg-white text-dark font-weight-bold">
                Error: ${e.message}</td></tr>`;
        }
    }

    /* ─── Stats ─────────────────────────────────────────────── */
    function updateStats() {
        el('stat-total').textContent   = records.length;
        el('stat-present').textContent = records.filter(r => r.status === 'present').length;
        el('stat-late').textContent    = records.filter(r => r.status === 'late').length;
        el('stat-absent').textContent  = records.filter(r => r.status === 'absent').length;
        el('stat-issues').textContent  = records.filter(r => ['incomplete', 'absent'].includes(r.status)).length;

        document.querySelectorAll('.stat-card').forEach(c => {
            c.classList.remove('border-secondary', 'bg-light');
            c.classList.add('border', 'bg-white');
        });
        const active = document.querySelector(`.stat-card[data-filter="${activeFilter}"]`);
        if (active) {
            active.classList.remove('border', 'bg-white');
            active.classList.add('border-secondary', 'bg-light');
        }
    }

    function updateTitle() {
        if (view === 'daily') {
            el('records-title').textContent = `Attendance — ${el('f-date').value}`;
        } else {
            const empId = el('f-employee-id').value;
            const emp   = employees.find(e => e.id === empId);
            const month = el('f-month');
            el('records-title').textContent = emp
                ? `${emp.fullName} — ${month.options[month.selectedIndex].text} ${el('f-year').value}`
                : 'Attendance Records';
        }
    }

    /* ─── Render Table ──────────────────────────────────────── */
    function renderTable() {
        if (!filtered.length) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 bg-white text-muted font-weight-bold">
                No records found for this criteria.</td></tr>`;
            el('table-footer').textContent  = 'No results';
            el('records-count').textContent = '0';
            return;
        }

        const statusBadge = s => {
            const cls = {
                present:    'bg-secondary text-white',
                late:       'bg-light border text-dark',
                absent:     'bg-light border border-secondary text-muted',
                incomplete: 'bg-light border text-muted',
                half_day:   'bg-white border border-dark text-dark',
                leave:      'bg-white border border-secondary text-dark',
                holiday:    'bg-secondary text-white',
                rest_day:   'bg-light border text-muted',
            }[s] ?? 'bg-light border text-dark';
            return `<span class="badge ${cls} px-2 py-1 text-uppercase">${s?.replace('_', ' ') ?? '—'}</span>`;
        };

        tbody.innerHTML = filtered.map(r => {
            const name     = r.user?.fullName ?? '—';
            const dept     = r.user?.department ?? '—';
            const initials = name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
            const hasIssue = ['incomplete', 'absent'].includes(r.status);

            return `<tr class="border-bottom ${hasIssue ? 'bg-light' : 'bg-white'}">
                <td class="text-center ps-3 py-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white font-weight-bold shadow-sm"
                          style="width:36px;height:36px;font-size:.8rem">${initials}</span>
                </td>
                <td class="py-3">
                    <div class="font-weight-bold text-dark">${name}</div>
                    <div class="text-muted small font-weight-bold text-uppercase" style="font-size:0.7rem;">
                        ${r.user_id} <span class="mx-1">|</span> ${dept}
                        ${view === 'employee' ? `<span class="mx-1">|</span> <span class="text-dark">${r.date}</span>` : ''}
                    </div>
                </td>
                <td class="py-3 font-weight-bold text-secondary">
                    ${r.time_in  ? r.time_in.slice(0,5)  : '<span class="text-muted fw-normal">—</span>'}
                </td>
                <td class="py-3 font-weight-bold text-secondary">
                    ${r.time_out ? r.time_out.slice(0,5) : '<span class="text-muted fw-normal">—</span>'}
                </td>
                <td class="py-3 font-weight-bold text-dark">
                    ${r.hours_worked > 0 ? parseFloat(r.hours_worked).toFixed(2) : '<span class="text-muted fw-normal">—</span>'}
                </td>
                <td class="py-3 font-weight-bold">
                    ${r.late_minutes > 0
                        ? `<span class="text-dark">${parseFloat(r.late_minutes).toFixed(0)} min</span>`
                        : '<span class="text-muted fw-normal">—</span>'}
                </td>
                <td class="py-3 font-weight-bold">
                    ${r.undertime_minutes > 0
                        ? `<span class="text-dark">${parseFloat(r.undertime_minutes).toFixed(0)} min</span>`
                        : '<span class="text-muted fw-normal">—</span>'}
                </td>
                <td class="py-3">${statusBadge(r.status)}</td>
                <td class="py-3 text-center pe-3">
                    <button class="btn btn-sm btn-light border text-dark font-weight-bold px-3 btn-edit-record shadow-sm"
                            data-record='${JSON.stringify({
                                id:       r.id,
                                user_id:  r.user_id,
                                date:     r.date,
                                time_in:  r.time_in  ? r.time_in.slice(0,5)  : '',
                                time_out: r.time_out ? r.time_out.slice(0,5) : '',
                                status:   r.status,
                                notes:    r.notes ?? '',
                            }).replace(/'/g, "&apos;")}'
                            title="Edit">Edit</button>
                </td>
            </tr>`;
        }).join('');

        el('table-footer').textContent  = `Showing ${filtered.length} of ${records.length} record(s)`;
        el('records-count').textContent = `${filtered.length}`;
    }

    /* ─── View Toggle ───────────────────────────────────────── */
    function setView(v) {
        view = v;

        el('filters-daily').classList.toggle('d-none',    v === 'employee');
        el('filters-employee').classList.toggle('d-none', v === 'daily');

        el('btn-view-daily').className    = v === 'daily'
            ? 'btn btn-secondary font-weight-bold px-4 shadow-sm'
            : 'btn btn-outline-dark font-weight-bold px-4';
        el('btn-view-employee').className = v === 'employee'
            ? 'btn btn-secondary font-weight-bold px-4 shadow-sm'
            : 'btn btn-outline-dark font-weight-bold px-4';

        records      = [];
        filtered     = [];
        activeFilter = 'all';
        updateStats();

        tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 bg-white text-muted font-weight-bold">
            ${v === 'daily' ? 'Select a date and click Load.' : 'Select an employee and click Load.'}
        </td></tr>`;

        el('records-title').textContent = 'Attendance Records';
        el('table-footer').textContent  = '—';
        el('records-count').textContent = '—';
    }

    /* ─── Add / Edit Modal ──────────────────────────────────── */
    function openAdd() {
        el('record-modal-title').textContent = 'Add Attendance Record';
        el('r-record-id').value = '';
        el('r-date').value      = el('f-date').value || '{{ date('Y-m-d') }}';
        el('r-time-in').value   = '';
        el('r-time-out').value  = '';
        el('r-status').value    = '';
        el('r-notes').value     = '';
        el('r-preview').classList.add('d-none');
        clearEmployeeSearch('r-employee-search', 'r-user-id', 'r-employee-hint');
        getBsModal().show();
    }

    function openEdit(data) {
        el('record-modal-title').textContent = 'Edit Attendance Record';
        el('r-record-id').value = data.id      ?? '';
        el('r-date').value      = data.date    ?? '';
        el('r-time-in').value   = data.time_in  ?? '';
        el('r-time-out').value  = data.time_out ?? '';
        el('r-status').value    = data.status   ?? '';
        el('r-notes').value     = data.notes    ?? '';
        el('r-preview').classList.add('d-none');
        setEmployeeSearch('r-employee-search', 'r-user-id', 'r-employee-hint', data.user_id ?? null);
        getBsModal().show();
    }

    function previewComputed() {
        const tin  = el('r-time-in').value;
        const tout = el('r-time-out').value;
        if (!tin || !tout) { el('r-preview').classList.add('d-none'); return; }

        const [h1, m1] = tin.split(':').map(Number);
        const [h2, m2] = tout.split(':').map(Number);
        let mins = (h2 * 60 + m2) - (h1 * 60 + m1);
        if (mins < 0) mins += 1440;

        el('r-prev-hours').textContent = `${(mins / 60).toFixed(2)} hrs`;
        el('r-prev-late').textContent  = '(server)';
        el('r-prev-ut').textContent    = '(server)';
        el('r-preview').classList.remove('d-none');
    }

    /* ─── Save Record ───────────────────────────────────────── */
    async function saveRecord() {
        const btn    = el('btn-save-record');
        const userId = el('r-user-id').value;
        const date   = el('r-date').value;

        if (!userId || !date) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Employee and date are required.', confirmButtonColor: '#1a1a1a' });
            return;
        }

        btn.disabled    = true;
        btn.textContent = 'Saving…';

        try {
            await api(ROUTES.upsert, {
                method: 'POST',
                body: JSON.stringify({
                    user_id:  userId,
                    date:     date,
                    time_in:  el('r-time-in').value  || null,
                    time_out: el('r-time-out').value || null,
                    status:   el('r-status').value   || null,
                    notes:    el('r-notes').value    || null,
                }),
            });

            Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
            getBsModal().hide();
            await loadRecords();

        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#1a1a1a' });
        } finally {
            btn.disabled    = false;
            btn.textContent = 'Save Record';
        }
    }

    /* ─── Stat card filter ──────────────────────────────────── */
    function applyStatFilter(filter) {
        activeFilter = filter;
        filtered = filter === 'all'
            ? [...records]
            : filter === 'issues'
                ? records.filter(r => ['incomplete', 'absent'].includes(r.status))
                : records.filter(r => r.status === filter);
        updateStats();
        renderTable();
    }

    /* ─── Bind ──────────────────────────────────────────────── */
    function bind() {
        el('btn-view-daily').addEventListener('click',    () => setView('daily'));
        el('btn-view-employee').addEventListener('click', () => setView('employee'));
        el('btn-load-daily').addEventListener('click',    loadRecords);
        el('btn-load-employee').addEventListener('click', loadRecords);
        el('btn-add-record').addEventListener('click',    openAdd);
        el('btn-save-record').addEventListener('click',   saveRecord);

        el('f-date').addEventListener('keydown', e => { if (e.key === 'Enter') loadRecords(); });
        el('r-time-in').addEventListener('change',  previewComputed);
        el('r-time-out').addEventListener('change', previewComputed);

        tbody.addEventListener('click', e => {
            const btn = e.target.closest('.btn-edit-record');
            if (!btn) return;
            const data = JSON.parse(btn.dataset.record.replace(/&apos;/g, "'"));
            openEdit(data);
        });

        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('click', () => applyStatFilter(card.dataset.filter));
        });
    }

    /* ─── Init ──────────────────────────────────────────────── */
    async function init() {
        bind();
        await loadEmployees();
        await loadRecords();
    }

    return { init };
})();

document.addEventListener('DOMContentLoaded', () => TeamAttendance.init());
</script>
@endpush