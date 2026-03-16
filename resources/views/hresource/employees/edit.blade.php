@extends('layouts.main')

@section('title', 'Edit — ' . $employee->fullName)

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('hresource.employees.index') }}" class="text-secondary text-decoration-none">Employees</a></li>
        <li class="breadcrumb-item"><a href="{{ route('hresource.employees.show', $employee) }}" class="text-secondary text-decoration-none">{{ $employee->fullName }}</a></li>
        <li class="breadcrumb-item active text-muted">Edit</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark text-uppercase">Update Profile</h4>
        <small class="text-muted font-weight-bold text-uppercase">Editing information for: <span class="text-dark">{{ $employee->fullName }}</span></small>
    </div>
    <span class="badge bg-light border text-dark px-3 py-2 text-uppercase font-weight-bold shadow-sm">ID: {{ $employee->id }}</span>
</div>

<div class="card shadow-sm border-0 mb-5">
    <div class="card-body p-4 p-lg-5">
        @include('hresource.employees._form', [
            'employee'          => $employee,
            'scheduleTemplates' => $scheduleTemplates,
            'action'            => route('hresource.employees.update', $employee),
            'method'            => 'PATCH',
        ])
    </div>
</div>

@endsection