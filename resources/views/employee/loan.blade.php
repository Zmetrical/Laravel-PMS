@extends('layouts.main')

@section('title', 'My Loans')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">My Loans</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">My Loans</h4>
    </div>
</div>

{{-- Info Banner --}}
<div class="alert alert-light border border-secondary shadow-sm mb-5 p-4">
    <h6 class="font-weight-bold text-dark text-uppercase mb-2">How to Apply for SSS / PAG-IBIG Loans</h6>
    <p class="mb-3 small text-muted font-weight-bold">
        To apply for SSS or PAG-IBIG loans, please visit the respective government offices or their online portals.
        Once approved, HR will encode your loan details into the system for automatic monthly deduction.
    </p>
    <div class="d-flex gap-3 mt-1">
        <a href="https://www.sss.gov.ph" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark btn-sm font-weight-bold px-3">
            SSS Website &rarr;
        </a>
        <a href="https://www.pagibigfund.gov.ph" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark btn-sm font-weight-bold px-3">
            PAG-IBIG Website &rarr;
        </a>
    </div>
</div>

@php
    $activeLoans    = collect($loansData)->where('status', 'active')->values();
    $completedLoans = collect($loansData)->where('status', 'completed')->values();
    $totalBalance   = $activeLoans->sum('remaining_balance');
@endphp

@if(count($loansData) > 0)

    {{-- Summary Cards --}}
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
                <span class="text-muted small font-weight-bold text-uppercase mb-2">Active Loans</span>
                <span class="h2 font-weight-bold text-dark mb-0">{{ $activeLoans->count() }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
                <span class="text-muted small font-weight-bold text-uppercase mb-2">Completed Loans</span>
                <span class="h2 font-weight-bold text-dark mb-0">{{ $completedLoans->count() }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border border-secondary rounded bg-light p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
                <span class="text-dark small font-weight-bold text-uppercase mb-2">Total Remaining Balance</span>
                <span class="h3 font-weight-bold text-dark mb-0">₱{{ number_format($totalBalance, 2) }}</span>
            </div>
        </div>
    </div>

@endif

{{-- Active Loans --}}
@if($activeLoans->count() > 0)

    <h6 class="font-weight-bold text-dark text-uppercase mb-3">Active Loans</h6>

    @foreach($activeLoans as $loan)
        @php
            $progress = $loan['term_months'] > 0
                ? min(100, round(($loan['payments_made'] / $loan['term_months']) * 100))
                : 0;
        @endphp

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">{{ $loan['loan_type_name'] }}</h6>
                <span class="badge bg-secondary px-3 py-2 text-uppercase" style="letter-spacing: 1px;">Active</span>
            </div>
            <div class="card-body p-4">

                {{-- Amounts --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="border rounded bg-light p-3 text-center h-100 d-flex flex-column justify-content-center py-4">
                            <span class="text-muted small font-weight-bold text-uppercase mb-2">Total Loan Amount</span>
                            <span class="h5 font-weight-bold text-dark mb-0">₱{{ number_format($loan['amount'], 2) }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded bg-light p-3 text-center h-100 d-flex flex-column justify-content-center py-4">
                            <span class="text-muted small font-weight-bold text-uppercase mb-2">Monthly Deduction</span>
                            <span class="h5 font-weight-bold text-dark mb-0">₱{{ number_format($loan['monthly_amortization'], 2) }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border border-secondary rounded bg-white shadow-sm p-3 text-center h-100 d-flex flex-column justify-content-center py-4">
                            <span class="text-dark small font-weight-bold text-uppercase mb-2">Remaining Balance</span>
                            <span class="h4 font-weight-bold text-dark mb-0">₱{{ number_format($loan['remaining_balance'], 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Progress --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small font-weight-bold text-uppercase">Payment Progress</span>
                        <span class="small font-weight-bold text-dark text-uppercase">
                            {{ $loan['payments_made'] }} of {{ $loan['term_months'] }} payments
                        </span>
                    </div>
                    <div class="progress bg-light border" style="height: 12px; border-radius: 6px;">
                        <div class="progress-bar bg-secondary"
                             role="progressbar"
                             style="width: {{ $progress }}%"
                             aria-valuenow="{{ $progress }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="d-flex flex-wrap gap-4 pt-3 border-top">
                    <div>
                        <span class="text-muted small font-weight-bold text-uppercase">Start Date:</span>
                        <span class="font-weight-bold text-dark ms-1">
                            {{ \Carbon\Carbon::parse($loan['start_date'])->format('M d, Y') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-muted small font-weight-bold text-uppercase">Loan Type:</span>
                        <span class="font-weight-bold text-dark ms-1">{{ strtoupper($loan['loan_type']) }}</span>
                    </div>
                    @if($loan['notes'])
                        <div>
                            <span class="text-muted small font-weight-bold text-uppercase">Note:</span>
                            <span class="font-weight-bold text-dark ms-1">{{ $loan['notes'] }}</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @endforeach

@else

    {{-- No Active Loans --}}
    <div class="text-center py-5 bg-light rounded border mt-3 mb-5">
        <span class="font-weight-bold text-dark d-block mb-1">No Active Loans</span>
        <small class="text-muted font-weight-bold text-uppercase">You don't have any active SSS or PAG-IBIG loans at the moment.</small>
    </div>

@endif

{{-- Completed Loans --}}
@if($completedLoans->count() > 0)

    <h6 class="font-weight-bold text-dark text-uppercase mb-3">Completed Loans</h6>
    
    <div class="row g-3 mb-5">
        @foreach($completedLoans as $loan)
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <div class="font-weight-bold text-dark text-uppercase mb-2">{{ $loan['loan_type_name'] }}</div>
                            <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">
                                Amount: <span class="text-dark">₱{{ number_format($loan['amount'], 2) }}</span>
                                <span class="mx-2">|</span>
                                <span class="text-dark">{{ $loan['payments_made'] }}</span> payments
                                @if($loan['completed_date'])
                                    <span class="mx-2">|</span>
                                    Completed: <span class="text-dark">{{ \Carbon\Carbon::parse($loan['completed_date'])->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="badge bg-light border text-dark px-3 py-2 text-uppercase">Paid</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endif

@endsection