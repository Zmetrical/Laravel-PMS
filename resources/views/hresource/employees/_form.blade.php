{{-- resources/views/hresource/employees/_form.blade.php --}}

<form action="{{ $action }}" method="POST">
    @csrf
    @method($method)

    {{-- Nav Tabs --}}
    <div class="mb-4">
        <ul class="nav nav-tabs border-bottom-0 gap-1 bg-light p-1 rounded shadow-sm d-inline-flex" id="emp-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link font-weight-bold px-4 py-2 {{ $errors->hasAny(['firstName','middleName','lastName','fullName','gender','civilStatus','dateOfBirth','email','phoneNumber']) ? 'text-danger' : 'text-muted' }} active"
                   data-bs-toggle="tab" href="#tab-personal" style="font-size: 0.75rem; text-transform: uppercase;">
                    Personal
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold px-4 py-2 {{ $errors->hasAny(['department','position','branch','employmentStatus','role','hireDate','basicSalary','username','password']) ? 'text-danger' : 'text-muted' }}"
                   data-bs-toggle="tab" href="#tab-employment" style="font-size: 0.75rem; text-transform: uppercase;">
                    Employment
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold px-4 py-2 {{ $errors->hasAny(['addressStreet','addressBarangay','addressCity','addressProvince','addressRegion','addressZipCode']) ? 'text-danger' : 'text-muted' }}"
                   data-bs-toggle="tab" href="#tab-address" style="font-size: 0.75rem; text-transform: uppercase;">
                    Address
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold px-4 py-2 text-muted" data-bs-toggle="tab" href="#tab-schedule" style="font-size: 0.75rem; text-transform: uppercase;">
                    Schedule
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content bg-light p-4 rounded border shadow-sm">

        {{-- ── Personal ──────────────────────────────────────── --}}
        <div class="tab-pane fade show active" id="tab-personal">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">First Name</label>
                    <input type="text" name="firstName" class="form-control font-weight-bold text-dark @error('firstName') is-invalid @enderror"
                           value="{{ old('firstName', $employee->firstName ?? '') }}">
                    @error('firstName') <div class="invalid-feedback font-weight-bold">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Middle Name</label>
                    <input type="text" name="middleName" class="form-control font-weight-bold text-dark @error('middleName') is-invalid @enderror"
                           value="{{ old('middleName', $employee->middleName ?? '') }}">
                    @error('middleName') <div class="invalid-feedback font-weight-bold">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Last Name</label>
                    <input type="text" name="lastName" class="form-control font-weight-bold text-dark @error('lastName') is-invalid @enderror"
                           value="{{ old('lastName', $employee->lastName ?? '') }}">
                    @error('lastName') <div class="invalid-feedback font-weight-bold">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small font-weight-bold text-uppercase text-dark mb-2">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="fullName" class="form-control font-weight-bold text-dark border-secondary shadow-sm @error('fullName') is-invalid @enderror"
                           value="{{ old('fullName', $employee->fullName ?? '') }}">
                    @error('fullName') <div class="invalid-feedback font-weight-bold">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Gender</label>
                    <select name="gender" class="form-select font-weight-bold text-dark @error('gender') is-invalid @enderror">
                        <option value="">— Select —</option>
                        @foreach(['Male','Female','Other'] as $g)
                            <option value="{{ $g }}" {{ old('gender', $employee->gender ?? '') === $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Civil Status</label>
                    <select name="civilStatus" class="form-select font-weight-bold text-dark @error('civilStatus') is-invalid @enderror">
                        <option value="">— Select —</option>
                        @foreach(['Single','Married','Widowed','Separated'] as $cs)
                            <option value="{{ $cs }}" {{ old('civilStatus', $employee->civilStatus ?? '') === $cs ? 'selected' : '' }}>{{ $cs }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Date of Birth</label>
                    <input type="date" name="dateOfBirth" class="form-control font-weight-bold text-dark @error('dateOfBirth') is-invalid @enderror"
                           value="{{ old('dateOfBirth', isset($employee->dateOfBirth) ? $employee->dateOfBirth->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Email</label>
                    <input type="email" name="email" class="form-control font-weight-bold text-dark @error('email') is-invalid @enderror"
                           value="{{ old('email', $employee->email ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Phone Number</label>
                    <input type="text" name="phoneNumber" class="form-control font-weight-bold text-dark @error('phoneNumber') is-invalid @enderror"
                           value="{{ old('phoneNumber', $employee->phoneNumber ?? '') }}">
                </div>
            </div>
        </div>

        {{-- ── Employment ─────────────────────────────────────── --}}
        <div class="tab-pane fade" id="tab-employment">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Department</label>
                    <input type="text" name="department" list="dept-list" class="form-control font-weight-bold text-dark @error('department') is-invalid @enderror"
                           value="{{ old('department', $employee->department ?? '') }}">
                    <datalist id="dept-list"></datalist>
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Position</label>
                    <input type="text" name="position" list="pos-list" class="form-control font-weight-bold text-dark @error('position') is-invalid @enderror"
                           value="{{ old('position', $employee->position ?? '') }}">
                    <datalist id="pos-list"></datalist>
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-uppercase text-dark mb-2">Branch <span class="text-danger">*</span></label>
                    <input type="text" name="branch" list="branch-list" class="form-control border-secondary font-weight-bold text-dark @error('branch') is-invalid @enderror"
                           value="{{ old('branch', $employee->branch ?? '') }}">
                    <datalist id="branch-list"></datalist>
                </div>

                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-uppercase text-dark mb-2">Status <span class="text-danger">*</span></label>
                    <select name="employmentStatus" class="form-select font-weight-bold text-dark border-secondary @error('employmentStatus') is-invalid @enderror">
                        @foreach(['probationary','regular','resigned','terminated'] as $s)
                            <option value="{{ $s }}" {{ old('employmentStatus', $employee->employmentStatus ?? 'probationary') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-uppercase text-dark mb-2">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select font-weight-bold text-dark border-secondary @error('role') is-invalid @enderror">
                        @foreach(['employee','hr','accounting','admin'] as $r)
                            <option value="{{ $r }}" {{ old('role', $employee->role ?? 'employee') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Hire Date</label>
                    <input type="date" name="hireDate" class="form-control font-weight-bold text-dark @error('hireDate') is-invalid @enderror"
                           value="{{ old('hireDate', isset($employee->hireDate) ? $employee->hireDate->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold text-uppercase text-dark mb-2">Basic Salary (₱) <span class="text-danger">*</span></label>
                    <input type="number" name="basicSalary" id="f-basicSalary" class="form-control border-secondary font-weight-bold text-dark @error('basicSalary') is-invalid @enderror"
                           min="0" step="0.01" placeholder="0.00" value="{{ old('basicSalary', $employee->basicSalary ?? '') }}">
                </div>
                <div class="col-12">
                    <div class="border rounded bg-white p-3 shadow-sm d-flex gap-4">
                        <div class="small font-weight-bold text-uppercase">Daily Rate: <span class="text-secondary ms-1" id="f-daily-preview">—</span></div>
                        <div class="small font-weight-bold text-uppercase">Hourly Rate: <span class="text-secondary ms-1" id="f-hourly-preview">—</span></div>
                    </div>
                </div>

                @if(!isset($employee))
                <div class="col-md-6 border-top pt-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Username</label>
                    <input type="text" name="username" class="form-control font-weight-bold text-dark @error('username') is-invalid @enderror" placeholder="Leave blank for auto-generation">
                </div>
                <div class="col-md-6 border-top pt-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Password</label>
                    <input type="password" name="password" class="form-control font-weight-bold text-dark @error('password') is-invalid @enderror" placeholder="Default: password">
                </div>
                @endif
            </div>
        </div>

        {{-- ── Address ─────────────────────────────────────────── --}}
        <div class="tab-pane fade" id="tab-address">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Street / House No.</label>
                    <input type="text" name="addressStreet" class="form-control font-weight-bold text-dark @error('addressStreet') is-invalid @enderror"
                           value="{{ old('addressStreet', $employee->addressStreet ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Barangay</label>
                    <input type="text" name="addressBarangay" class="form-control font-weight-bold text-dark @error('addressBarangay') is-invalid @enderror"
                           value="{{ old('addressBarangay', $employee->addressBarangay ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">City / Municipality</label>
                    <input type="text" name="addressCity" class="form-control font-weight-bold text-dark @error('addressCity') is-invalid @enderror"
                           value="{{ old('addressCity', $employee->addressCity ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Province</label>
                    <input type="text" name="addressProvince" class="form-control font-weight-bold text-dark @error('addressProvince') is-invalid @enderror"
                           value="{{ old('addressProvince', $employee->addressProvince ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Region</label>
                    <input type="text" name="addressRegion" class="form-control font-weight-bold text-dark @error('addressRegion') is-invalid @enderror"
                           value="{{ old('addressRegion', $employee->addressRegion ?? '') }}">
                </div>
            </div>
        </div>

        {{-- ── Schedule ─────────────────────────────────────────── --}}
        <div class="tab-pane fade" id="tab-schedule">
            <div class="row g-4 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Schedule Template</label>
                    <select name="template_id" id="f-template-id" class="form-select font-weight-bold text-dark border-secondary shadow-sm">
                        <option value="">— No Schedule Assigned —</option>
                        @foreach($scheduleTemplates as $tpl)
                            <option value="{{ $tpl->id }}"
                                {{ old('template_id', $employee->currentSchedule->template_id ?? '') == $tpl->id ? 'selected' : '' }}>
                                {{ $tpl->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small font-weight-bold text-uppercase text-muted mb-2">Effective Date</label>
                    <input type="date" name="effective_date" id="f-effective-date" class="form-control font-weight-bold text-dark"
                           value="{{ old('effective_date', isset($employee->currentSchedule) ? $employee->currentSchedule->effective_date->format('Y-m-d') : date('Y-m-d')) }}">
                </div>
            </div>

            <div id="schedule-preview" class="mt-4 border rounded p-4 bg-white shadow-sm d-none">
                <span class="text-muted small font-weight-bold text-uppercase border-bottom pb-2 d-block mb-3">Weekly Schedule Preview</span>
                <div class="d-flex gap-2 flex-wrap" id="schedule-days"></div>
            </div>
        </div>

    </div>

    {{-- Footer Buttons --}}
    <div class="d-flex justify-content-end gap-3 mt-5">
        <a href="{{ route('hresource.employees.index') }}" class="btn btn-outline-dark font-weight-bold px-4 py-2">
            Cancel
        </a>
        <button type="submit" class="btn btn-secondary font-weight-bold px-5 py-2 shadow-sm">
            <i class="bi bi-floppy me-2"></i>{{ isset($employee) ? 'Save Changes' : 'Create Employee' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
(function () {
    const peso = n => '₱' + parseFloat(n || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const scheduleTemplates = @json($scheduleTemplates);

    // ── Salary preview ──────────────────────────────────────
    const salaryInput = document.getElementById('f-basicSalary');
    const dailyPrev   = document.getElementById('f-daily-preview');
    const hourlyPrev  = document.getElementById('f-hourly-preview');

    function refreshPreview(val) {
        const n = parseFloat(val);
        const ok = !isNaN(n) && n > 0;
        dailyPrev.textContent  = ok ? peso(n / 26)     : '—';
        hourlyPrev.textContent = ok ? peso(n / 26 / 8) : '—';
    }

    if (salaryInput) {
        salaryInput.addEventListener('input', e => refreshPreview(e.target.value));
        refreshPreview(salaryInput.value);
    }

    // ── Schedule preview ─────────────────────────────────────
    const templateSel  = document.getElementById('f-template-id');
    const previewWrap  = document.getElementById('schedule-preview');
    const previewDays  = document.getElementById('schedule-days');
    const dayNames     = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

    function renderSchedulePreview(templateId) {
        const tpl = scheduleTemplates.find(t => t.id == templateId);
        if (!tpl) { previewWrap.classList.add('d-none'); return; }

        previewDays.innerHTML = tpl.days.map(d =>
            `<div class="badge ${d.is_working_day ? 'bg-secondary text-white' : 'bg-light border text-muted'} px-3 py-2 text-uppercase text-center shadow-sm" style="min-width: 60px;">
                <div style="font-size: 0.6rem;">${dayNames[d.day_of_week]}</div>
                ${d.is_working_day && d.shift_in ? `<div class="fw-normal" style="font-size: 0.6rem; opacity: 0.8;">${d.shift_in.slice(0,5)}–${(d.shift_out ?? '').slice(0,5)}</div>` : ''}
            </div>`
        ).join('');
        previewWrap.classList.remove('d-none');
    }

    if (templateSel) {
        templateSel.addEventListener('change', e => renderSchedulePreview(e.target.value));
        if (templateSel.value) renderSchedulePreview(templateSel.value);
    }

    // ── Auto-open errored tab ───────────────────────────────
    const firstErrorTab = document.querySelector('#emp-tabs .nav-link.text-danger');
    if (firstErrorTab) {
        new bootstrap.Tab(firstErrorTab).show();
    }
})();
</script>
@endpush