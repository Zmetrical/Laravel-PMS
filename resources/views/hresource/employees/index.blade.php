@extends('layouts.main')

@section('title', 'Employees')

@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
    <li class="breadcrumb-item active text-muted">Employees</li>
</ol>
@endsection

@section('content')

@include('components.alerts')

{{-- Stats --}}
<div class="row g-3 mb-5">
    @foreach([
        ['label' => 'Total Employees', 'value' => $stats['total']],
        ['label' => 'Active',          'value' => $stats['active']],
        ['label' => 'Regular',         'value' => $stats['regular']],
        ['label' => 'Probationary',    'value' => $stats['probationary']],
    ] as $stat)
    <div class="col-6 col-md-3">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">{{ $stat['label'] }}</span>
            <span class="h2 font-weight-bold text-dark mb-0">{{ $stat['value'] }}</span>
        </div>
    </div>
    @endforeach
</div>

{{-- Main Card --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Employee Directory</h6>
        <a href="{{ route('hresource.employees.create') }}" class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm">
            <i class="bi bi-person-plus-fill me-2"></i>Add Employee
        </a>
    </div>

    {{-- Filter Form --}}
    <div class="card-body bg-light border-bottom p-4">
        <form method="GET" action="{{ route('hresource.employees.index') }}">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 font-weight-bold text-dark ps-0"
                               placeholder="Name, ID, or position…" value="{{ $filters['search'] ?? '' }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="department" class="form-select shadow-sm font-weight-bold text-dark">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ ($filters['department'] ?? '') === $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="branch" class="form-select shadow-sm font-weight-bold text-dark">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch }}" {{ ($filters['branch'] ?? '') === $branch ? 'selected' : '' }}>
                                {{ $branch }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="employmentStatus" class="form-select shadow-sm font-weight-bold text-dark">
                        <option value="">All Status</option>
                        @foreach(['probationary','regular','resigned','terminated'] as $s)
                            <option value="{{ $s }}" {{ ($filters['employmentStatus'] ?? '') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-dark font-weight-bold w-100">Filter</button>
                    @if(array_filter($filters))
                        <a href="{{ route('hresource.employees.index') }}" class="btn btn-light border font-weight-bold w-100">Clear</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 ps-4 py-3">Employee</th>
                        <th class="border-0 py-3">Department / Position</th>
                        <th class="border-0 py-3">Branch</th>
                        <th class="border-0 py-3 text-center">Status</th>
                        <th class="border-0 py-3">Hire Date</th>
                        <th class="border-0 pe-4 py-3 text-center" style="width:180px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    @php
                        $initials = collect(explode(' ', $emp->fullName))->map(fn($n) => substr($n, 0, 1))->join('');
                        $initials = strtoupper(substr($initials, 0, 2));
                    @endphp
                    <tr class="border-bottom bg-white {{ $emp->isActive ? '' : 'opacity-75' }}">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white font-weight-bold shadow-sm" style="width:38px;height:38px;font-size:.8rem">
                                    {{ $initials }}
                                </span>
                                <div>
                                    <div class="font-weight-bold text-dark mb-0">{{ $emp->fullName }}</div>
                                    <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 0.65rem;">
                                        ID: {{ $emp->id }} @if($emp->email) <span class="mx-1">|</span> {{ $emp->email }} @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="font-weight-bold text-dark">{{ $emp->department ?? '—' }}</div>
                            <div class="text-muted small font-weight-bold text-uppercase" style="font-size: 0.65rem;">{{ $emp->position ?? '—' }}</div>
                        </td>
                        <td class="py-3 font-weight-bold text-secondary text-uppercase small">
                            {{ $emp->branch ?? '—' }}
                        </td>
                        <td class="py-3 text-center">
                            <span class="badge {{ $emp->isActive ? 'bg-secondary text-white' : 'bg-light border text-muted' }} px-2 py-1 text-uppercase" style="font-size: 0.65rem;">
                                {{ $emp->employmentStatus }}
                            </span>
                        </td>
                        <td class="py-3 font-weight-bold text-dark small">
                            {{ $emp->hireDate?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="pe-4 py-3 text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('hresource.employees.show', $emp) }}"
                                   class="btn btn-sm btn-light border text-dark font-weight-bold px-3 shadow-sm">View</a>
                                <a href="{{ route('hresource.employees.edit', $emp) }}"
                                   class="btn btn-sm btn-outline-dark font-weight-bold px-3">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 bg-white text-muted">
                            <i class="bi bi-people text-muted mb-3 d-block" style="font-size: 3rem;"></i>
                            <span class="font-weight-bold text-dark d-block mb-1">No Employees Found</span>
                            <small class="text-muted font-weight-bold text-uppercase">Try adjusting your filters or adding a new employee.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer bg-light py-3 border-top d-flex align-items-center justify-content-between">
        <small class="text-muted font-weight-bold text-uppercase">
            Showing <span class="text-dark">{{ $employees->firstItem() ?? 0 }}</span> – 
            <span class="text-dark">{{ $employees->lastItem() ?? 0 }}</span> of 
            <span class="text-dark">{{ $employees->total() }}</span> employee(s)
        </small>
        {{ $employees->appends(request()->query())->links() }}
    </div>
</div>

@endsection