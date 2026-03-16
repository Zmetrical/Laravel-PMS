@extends('layouts.main')

@section('title', 'Payroll Summary — ' . $period->label)

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.payroll.periods.index') }}" class="text-secondary text-decoration-none">Payroll Periods</a></li>
        <li class="breadcrumb-item"><a href="{{ route('accounting.payroll.periods.records', $period) }}" class="text-secondary text-decoration-none">Records</a></li>
        <li class="breadcrumb-item active text-muted">Summary</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Payroll Summary</h4>
        <small class="text-muted font-weight-bold text-uppercase">
            {{ $period->label }} <span class="mx-2">|</span> Pay Date: <span class="text-dark">{{ $period->pay_date->format('M d, Y') }}</span>
            <span class="mx-2">|</span>
            @if ($period->isReleased())
                <span class="badge bg-secondary px-2 py-1 text-uppercase text-white">Released</span>
            @else
                <span class="badge bg-dark px-2 py-1 text-uppercase text-white">Closed</span>
            @endif
        </small>
    </div>
</div>

{{-- YOUR TAB DESIGN --}}
<div class="mb-5">
    <div class="d-inline-flex gap-1 bg-white border p-1 rounded shadow-sm">
        {{-- Process Tab (Only accessible when Processing) --}}
        @if ($period->isProcessing())
            <a href="{{ route('accounting.payroll.periods.process', $period) }}" 
               class="btn btn-sm font-weight-bold px-4 {{ request()->routeIs('accounting.payroll.periods.process') ? 'btn-secondary shadow-sm text-white' : 'btn-light border-0 text-muted' }}">
                Process
            </a>
        @else
            <span class="btn btn-sm font-weight-bold px-4 btn-light border-0 text-muted" 
                  style="opacity: 0.4; cursor: not-allowed;" title="Only available while processing">
                Process
            </span>
        @endif

        {{-- Records Tab (Accessible unless in Draft) --}}
        @if (!$period->isDraft())
            <a href="{{ route('accounting.payroll.periods.records', $period) }}" 
               class="btn btn-sm font-weight-bold px-4 {{ request()->routeIs('accounting.payroll.periods.records') ? 'btn-secondary shadow-sm text-white' : 'btn-light border-0 text-muted' }}">
                Records
            </a>
        @else
            <span class="btn btn-sm font-weight-bold px-4 btn-light border-0 text-muted" 
                  style="opacity: 0.4; cursor: not-allowed;" title="Not available in draft status">
                Records
            </span>
        @endif

        {{-- Summary Tab (Only accessible when Released or Closed) --}}
        @if ($period->isReleased() || $period->isClosed())
            <a href="{{ route('accounting.payroll.periods.summary', $period) }}" 
               class="btn btn-sm font-weight-bold px-4 {{ request()->routeIs('accounting.payroll.periods.summary') ? 'btn-secondary shadow-sm text-white' : 'btn-light border-0 text-muted' }}">
                Summary
            </a>
        @else
            <span class="btn btn-sm font-weight-bold px-4 btn-light border-0 text-muted" 
                  style="opacity: 0.4; cursor: not-allowed;" title="Available only after payroll is released">
                Summary
            </span>
        @endif
    </div>
</div>
{{-- ── Overview Cards ────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-5">
    <div class="col-md-3">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Employees Paid</span>
            <span class="h3 font-weight-bold text-dark mb-0">{{ $totals['count'] }}</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Total Gross Pay</span>
            <span class="h3 font-weight-bold text-dark mb-0">₱{{ number_format($totals['gross_pay'], 2) }}</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border rounded bg-white p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted small font-weight-bold text-uppercase mb-2">Total Deductions</span>
            <span class="h3 font-weight-bold text-dark mb-0">₱{{ number_format($totals['total_deductions'], 2) }}</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border border-secondary rounded bg-light p-4 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-dark small font-weight-bold text-uppercase mb-2">Total Net Pay</span>
            <span class="h2 font-weight-bold text-dark mb-0">₱{{ number_format($totals['net_pay'], 2) }}</span>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- ── Left Column ──────────────────────────────────────────────────── --}}
    <div class="col-lg-8">

        {{-- Department Breakdown --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">By Department</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="border-0 ps-4 py-3">Department</th>
                                <th class="border-0 py-3 text-center">Employees</th>
                                <th class="border-0 py-3 text-end">Gross Pay</th>
                                <th class="border-0 py-3 text-end">Deductions</th>
                                <th class="border-0 py-3 pe-4 text-end">Net Pay</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($byDepartment as $dept)
                            <tr class="border-bottom bg-white">
                                <td class="ps-4 py-3 font-weight-bold text-dark">
                                    {{ $dept['department'] }}
                                </td>
                                <td class="py-3 text-center font-weight-bold text-secondary">
                                    {{ $dept['count'] }}
                                </td>
                                <td class="py-3 text-end font-weight-bold text-secondary">
                                    ₱{{ number_format($dept['gross_pay'], 2) }}
                                </td>
                                <td class="py-3 text-end font-weight-bold text-danger">
                                    ₱{{ number_format($dept['total_deductions'], 2) }}
                                </td>
                                <td class="py-3 pe-4 text-end font-weight-bold text-dark">
                                    ₱{{ number_format($dept['net_pay'], 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5 bg-white font-weight-bold text-uppercase">No records</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if ($byDepartment->count() > 1)
                        <tfoot class="bg-light">
                            <tr>
                                <td class="font-weight-bold text-dark text-uppercase ps-4 py-3">Total</td>
                                <td class="font-weight-bold text-dark text-center py-3">{{ $totals['count'] }}</td>
                                <td class="font-weight-bold text-dark text-end py-3">₱{{ number_format($totals['gross_pay'], 2) }}</td>
                                <td class="font-weight-bold text-dark text-end py-3">₱{{ number_format($totals['total_deductions'], 2) }}</td>
                                <td class="font-weight-bold text-dark text-end pe-4 py-3">₱{{ number_format($totals['net_pay'], 2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Employee List (collapsed, for reference) --}}
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Employee Breakdown</h6>
                <button class="btn btn-sm btn-light border text-dark px-3 font-weight-bold"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#emp-collapse">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
            <div class="collapse" id="emp-collapse">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="border-0 ps-4 py-3">Employee</th>
                                    <th class="border-0 py-3 text-end">Gross</th>
                                    <th class="border-0 py-3 text-end">Deductions</th>
                                    <th class="border-0 pe-4 py-3 text-end">Net Pay</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records->sortBy(fn($r) => $r->employee?->fullName) as $record)
                                <tr class="border-bottom bg-white">
                                    <td class="ps-4 py-3">
                                        <div class="font-weight-bold text-dark mb-1">
                                            {{ $record->employee?->fullName ?? 'Unknown' }}
                                        </div>
                                        <div class="text-muted small font-weight-bold text-uppercase" style="font-size:0.7rem">
                                            {{ $record->employee?->department }}
                                        </div>
                                    </td>
                                    <td class="py-3 text-end font-weight-bold text-secondary">
                                        ₱{{ number_format($record->gross_pay, 2) }}
                                    </td>
                                    <td class="py-3 text-end font-weight-bold text-danger">
                                        ₱{{ number_format($record->total_deductions, 2) }}
                                    </td>
                                    <td class="py-3 pe-4 text-end font-weight-bold text-dark">
                                        ₱{{ number_format($record->net_pay, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Right Column ─────────────────────────────────────────────────── --}}
    <div class="col-lg-4">

        {{-- Earnings Breakdown --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="text-muted small font-weight-bold text-uppercase mb-0">Earnings Breakdown</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <tbody>
                        @php
                            $earningRows = [
                                'Basic Pay'           => $totals['basic_pay'],
                                'Overtime Pay'        => $totals['overtime_pay'],
                                'Night Differential'  => $totals['night_diff_pay'],
                                'Holiday Pay'         => $totals['holiday_pay'],
                                'Leave Pay'           => $totals['leave_pay'],
                                'Allowances'          => $totals['allowances'],
                            ];
                        @endphp
                        @foreach ($earningRows as $label => $amount)
                            @if ($amount > 0)
                            <tr class="border-bottom bg-white">
                                <td class="text-muted small font-weight-bold text-uppercase py-3 ps-4 border-0">{{ $label }}</td>
                                <td class="text-end font-weight-bold text-secondary py-3 pe-4 border-0">₱{{ number_format($amount, 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr class="border-top">
                            <td class="font-weight-bold text-dark ps-4 py-3">Gross Pay</td>
                            <td class="font-weight-bold text-dark text-end pe-4 py-3">₱{{ number_format($totals['gross_pay'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Deductions Breakdown --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="text-muted small font-weight-bold text-uppercase mb-0">Deductions Breakdown</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <tbody>
                        @php
                            $deductionRows = [
                                'SSS'              => $totals['sss'],
                                'PhilHealth'       => $totals['philhealth'],
                                'Pag-IBIG'         => $totals['pagibig'],
                                'Withholding Tax'  => $totals['withholding_tax'],
                                'Late'             => $totals['late_deductions'],
                                'Undertime'        => $totals['undertime_deductions'],
                                'Absent'           => $totals['absent_deductions'],
                                'Other'            => $totals['other_deductions'],
                            ];
                        @endphp
                        @foreach ($deductionRows as $label => $amount)
                            @if ($amount > 0)
                            <tr class="border-bottom bg-white">
                                <td class="text-muted small font-weight-bold text-uppercase py-3 ps-4 border-0">{{ $label }}</td>
                                <td class="text-end font-weight-bold text-danger py-3 pe-4 border-0">₱{{ number_format($amount, 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr class="border-top">
                            <td class="font-weight-bold text-dark ps-4 py-3">Total Deductions</td>
                            <td class="font-weight-bold text-dark text-end pe-4 py-3">₱{{ number_format($totals['total_deductions'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Net Pay Callout --}}
        <div class="border border-secondary rounded bg-white p-4 shadow-sm text-center">
            <h6 class="text-dark font-weight-bold text-uppercase mb-1">Total Net Pay</h6>
            <h2 class="font-weight-bold text-dark mb-0 display-6">₱{{ number_format($totals['net_pay'], 2) }}</h2>
        </div>

    </div>
</div>

@endsection