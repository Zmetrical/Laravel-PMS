@extends('layouts.main')

@section('title', 'Add Employee')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('hresource.employees.index') }}" class="text-secondary text-decoration-none">Employees</a></li>
        <li class="breadcrumb-item active text-muted">Add Employee</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

<div class="mb-4">
    <h4 class="mb-0 font-weight-bold text-dark text-uppercase">Create Employee Profile</h4>
    <small class="text-muted font-weight-bold text-uppercase">Enter personal and employment details for the new hire</small>
</div>

<div class="card shadow-sm border-0 mb-5">
    <div class="card-body p-4 p-lg-5">
        @include('hresource.employees._form', [
            'employee'          => null,
            'scheduleTemplates' => $scheduleTemplates,
            'action'            => route('hresource.employees.store'),
            'method'            => 'POST',
        ])
    </div>
</div>

@endsection