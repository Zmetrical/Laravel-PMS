@extends('layouts.main')

@section('title', $employee->fullName)

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('hresource.employees.index') }}" class="text-secondary text-decoration-none">Employees</a></li>
        <li class="breadcrumb-item active text-muted">{{ $employee->fullName }}</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-5">
    <div class="d-flex align-items-center gap-4">
        @php
            $initials = collect(explode(' ', $employee->fullName))->map(fn($n) => substr($n, 0, 1))->join('');
            $initials = strtoupper(substr($initials, 0, 2));
        @endphp
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white font-weight-bold shadow-sm"
              style="width:64px;height:64px;font-size:1.4rem;flex-shrink:0;">
            {{ $initials }}
        </span>
        <div>
            <h4 class="mb-1 font-weight-bold text-dark">{{ $employee->fullName }}</h4>
            <div class="text-muted font-weight-bold text-uppercase small" style="font-size: 0.75rem;">
                ID: <span class="text-dark">{{ $employee->id }}</span>
                @if($employee->position) <span class="mx-1">|</span> {{ $employee->position }} @endif
                @if($employee->department) <span class="mx-1">|</span> {{ $employee->department }} @endif
            </div>
        </div>
    </div>
    <div class="d-flex gap-3">
        <a href="{{ route('hresource.employees.edit', $employee) }}"
           class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm">
            <i class="bi bi-pencil-fill me-2"></i>Edit Profile
        </a>
        <form method="POST" action="{{ route('hresource.employees.toggle', $employee) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-outline-dark font-weight-bold px-4 py-2"
                    onclick="return confirm('{{ $employee->isActive ? 'Deactivate' : 'Activate' }} this employee?')">
                <i class="bi bi-{{ $employee->isActive ? 'person-dash-fill' : 'person-check-fill' }} me-2"></i>
                {{ $employee->isActive ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
    </div>
</div>

<div class="row g-4 mb-5">
    {{-- ── Left Column ──────────────────────────────────────── --}}
    <div class="col-lg-6">
        {{-- Personal --}}
        <div class="card shadow-sm border-0 mb-4 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Personal Information</h6>
            </div>
            <div class="card-body p-4 bg-light">
                <div class="row g-4">
                    <div class="col-md-6">
                        <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Full Name</span>
                        <span class="font-weight-bold text-dark">{{ $employee->fullName }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Gender / Status</span>
                        <span class="font-weight-bold text-dark">{{ $employee->gender ?? '—' }} <span class="mx-1">|</span> {{ $employee->civilStatus ?? '—' }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Date of Birth</span>
                        <span class="font-weight-bold text-dark">{{ $employee->dateOfBirth?->format('M d, Y') ?? '—' }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Contact No.</span>
                        <span class="font-weight-bold text-dark">{{ $employee->phoneNumber ?? '—' }}</span>
                    </div>
                    <div class="col-12 border-top pt-3">
                        <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Email Address</span>
                        <span class="font-weight-bold text-secondary">{{ $employee->email ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Right Column ─────────────────────────────────────── --}}
    <div class="col-lg-6">
        {{-- Employment --}}
        <div class="card shadow-sm border-0 mb-4 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Employment Details</h6>
            </div>
            <div class="card-body p-4 bg-light">
                <div class="row g-4">
                    <div class="col-md-6">
                        <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Department / Branch</span>
                        <span class="font-weight-bold text-dark">{{ $employee->department ?? '—' }} <span class="mx-1">|</span> {{ $employee->branch ?? '—' }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Status</span>
                        <span class="badge {{ $employee->isActive ? 'bg-secondary text-white' : 'bg-light border text-muted' }} px-2 py-1 text-uppercase" style="font-size: 0.65rem;">
                            {{ $employee->employmentStatus ?? '—' }}
                        </span>
                        @if(!$employee->isActive) <span class="badge bg-light border text-danger px-2 py-1 text-uppercase ms-1" style="font-size: 0.65rem;">Inactive</span> @endif
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Date Hired</span>
                        <span class="font-weight-bold text-dark">{{ $employee->hireDate?->format('M d, Y') ?? '—' }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Role</span>
                        <span class="badge bg-light border text-dark font-weight-bold px-2 py-1 text-uppercase" style="font-size: 0.65rem;">{{ $employee->role ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Address --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Home Address</h6>
            </div>
            <div class="card-body p-4 bg-light">
                <div class="row g-4">
                    <div class="col-md-8">
                        <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Street / Barangay</span>
                        <span class="font-weight-bold text-dark">{{ $employee->addressStreet ?? '—' }}, {{ $employee->addressBarangay ?? '—' }}</span>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">City / Province</span>
                        <span class="font-weight-bold text-dark">{{ $employee->addressCity ?? '—' }}, {{ $employee->addressProvince ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Compensation & Schedule --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Compensation</h6>
            </div>
            <div class="card-body p-4 bg-white">
                <div class="row g-3">
                    <div class="col-12 border-bottom pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small font-weight-bold text-uppercase">Monthly Basic Salary</span>
                            <span class="h5 font-weight-bold text-dark mb-0">₱{{ number_format($employee->basicSalary, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-12 border-bottom pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small font-weight-bold text-uppercase">Daily Rate</span>
                            <span class="font-weight-bold text-secondary">₱{{ number_format($employee->dailyRate, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small font-weight-bold text-uppercase">Hourly Rate</span>
                            <span class="font-weight-bold text-secondary">₱{{ number_format($employee->hourlyRate, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Work Schedule</h6>
            </div>
            <div class="card-body p-4 bg-white">
                @if($employee->currentSchedule && $employee->currentSchedule->template)
                    @php $tpl = $employee->currentSchedule->template; @endphp
                    <div class="font-weight-bold text-dark text-uppercase small mb-3">{{ $tpl->name }}</div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @php $dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']; @endphp
                        @foreach($tpl->days as $day)
                            <div class="badge {{ $day->is_working_day ? 'bg-secondary text-white' : 'bg-light border text-muted' }} px-3 py-2 text-uppercase text-center shadow-sm" style="min-width: 60px;">
                                <div style="font-size: 0.6rem;">{{ $dayNames[$day->day_of_week] }}</div>
                                @if($day->is_working_day && $day->shift_in)
                                    <div class="fw-normal" style="font-size: 0.6rem; opacity: 0.8;">{{ substr($day->shift_in, 0, 5) }}–{{ substr($day->shift_out ?? '', 0, 5) }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 0.65rem;">
                        Effective Date: <span class="text-dark">{{ $employee->currentSchedule->effective_date->format('M d, Y') }}</span>
                    </div>
                @else
                    <div class="text-center py-4">
                        <span class="text-muted font-weight-bold text-uppercase small">No schedule assigned</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Active Loans --}}
@if($employee->loans->count())
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Active Loan Accounts</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 ps-4 py-3">Loan Type</th>
                        <th class="border-0 py-3 text-end">Total Amount</th>
                        <th class="border-0 py-3 text-end">Monthly Amort.</th>
                        <th class="border-0 py-3 text-center">Start Date</th>
                        <th class="border-0 pe-4 py-3 text-end">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employee->loans as $loan)
                    <tr class="border-bottom bg-white">
                        <td class="ps-4 py-3 font-weight-bold text-dark text-uppercase small">{{ $loan->loan_type_name }}</td>
                        <td class="py-3 text-end font-weight-bold text-secondary">₱{{ number_format($loan->amount, 2) }}</td>
                        <td class="py-3 text-end font-weight-bold text-dark">₱{{ number_format($loan->monthly_amortization, 2) }}</td>
                        <td class="py-3 text-center text-muted font-weight-bold small">{{ \Carbon\Carbon::parse($loan->start_date)->format('M d, Y') }}</td>
                        <td class="pe-4 py-3 text-end">
                             <span class="badge bg-light border text-dark px-2 py-1 text-uppercase" style="font-size: 0.65rem;">Active Account</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Leave Balances --}}
@if($employee->leaveBalances->count())
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Leave Credit Balances</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 ps-4 py-3">Leave Category</th>
                        <th class="border-0 py-3 text-end">Entitled</th>
                        <th class="border-0 py-3 text-end">Used</th>
                        <th class="border-0 py-3 text-end">Pending</th>
                        <th class="border-0 pe-4 py-3 text-end">Current Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employee->leaveBalances as $lb)
                    <tr class="border-bottom bg-white">
                        <td class="ps-4 py-3 font-weight-bold text-dark text-uppercase small">{{ $lb->leaveType->name ?? '—' }}</td>
                        <td class="py-3 text-end font-weight-bold text-secondary small">{{ $lb->entitled_days }}</td>
                        <td class="py-3 text-end font-weight-bold text-secondary small">{{ $lb->used_days }}</td>
                        <td class="py-3 text-end font-weight-bold text-secondary small">{{ $lb->pending_days }}</td>
                        <td class="pe-4 py-3 text-end font-weight-bold text-dark h6 mb-0">{{ $lb->balance }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection