@extends('layouts.main')

@section('title', 'Payroll Periods')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">Payroll Periods</li>
    </ol>
@endsection

@section('content')

@include('components.alerts')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">Payroll Periods</h4>
        <small class="text-muted font-weight-bold text-uppercase">Manage cutoff periods and control payroll workflow</small>
    </div>
    <button class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createPeriodModal">
        New Period
    </button>
</div>


{{-- Periods Table --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Period History</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 ps-4 py-3">Period</th>
                        <th class="border-0 py-3">Type</th>
                        <th class="border-0 py-3">Coverage</th>
                        <th class="border-0 py-3">Pay Date</th>
                        <th class="border-0 py-3 text-center">Records</th>
                        <th class="border-0 py-3 text-center">Status</th>
                        <th class="border-0 py-3 text-end pe-4" style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($periods as $period)
                    <tr class="border-bottom bg-white">
                        <td class="ps-4 py-3 font-weight-bold text-dark">
                            {{ $period->label }}
                        </td>
                        <td class="py-3">
                            <span class="text-muted small font-weight-bold text-uppercase" style="font-size: 0.7rem;">{{ $period->period_type }}</span>
                        </td>
                        <td class="py-3 font-weight-bold text-secondary">
                            {{ $period->start_date->format('M d') }} &ndash; {{ $period->end_date->format('M d, Y') }}
                        </td>
                        <td class="py-3 font-weight-bold text-dark">
                            {{ $period->pay_date->format('M d, Y') }}
                        </td>
                        <td class="py-3 text-center font-weight-bold text-dark">
                            {{ $period->records_count }}
                        </td>
                        <td class="py-3 text-center">
                            @switch($period->status)
                                @case('draft')
                                    <span class="badge bg-light border text-muted px-2 py-1 text-uppercase">Draft</span>
                                    @break
                                @case('processing')
                                    <span class="badge bg-white border border-secondary text-dark px-2 py-1 text-uppercase">Processing</span>
                                    @break
                                @case('released')
                                    <span class="badge bg-secondary text-white px-2 py-1 text-uppercase">Released</span>
                                    @break
                                @case('closed')
                                    <span class="badge bg-dark text-white px-2 py-1 text-uppercase">Closed</span>
                                    @break
                            @endswitch
                        </td>
                        <td class="py-3 text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">

                                {{-- View / Process button (context-sensitive) --}}
                                @if ($period->isDraft())
                                    <form action="{{ route('accounting.payroll.periods.update-status', $period) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-dark font-weight-bold px-3 shadow-sm"
                                                title="Start Processing"
                                                onclick="return confirm('Mark this period as Processing?')">
                                            Process
                                        </button>
                                    </form>

                                @elseif ($period->isProcessing())
                                    <a href="{{ route('accounting.payroll.periods.process', $period) }}"
                                       class="btn btn-sm btn-outline-dark font-weight-bold px-2 shadow-sm" title="Compute Payroll">
                                        <i class="bi bi-calculator"></i>
                                    </a>
                                    <a href="{{ route('accounting.payroll.periods.records', $period) }}"
                                       class="btn btn-sm btn-light border text-dark font-weight-bold px-2" title="View Records">
                                        <i class="bi bi-list-ul"></i>
                                    </a>
                                    <form action="{{ route('accounting.payroll.periods.update-status', $period) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-secondary font-weight-bold px-2 shadow-sm"
                                                title="Release Payslips"
                                                onclick="return confirm('Release payslips? Employees will be able to view them.')">
                                            <i class="bi bi-send-fill"></i>
                                        </button>
                                    </form>

                                @elseif ($period->isReleased())
                                    <a href="{{ route('accounting.payroll.periods.records', $period) }}"
                                       class="btn btn-sm btn-light border text-dark font-weight-bold px-2" title="View Records">
                                        <i class="bi bi-list-ul"></i>
                                    </a>
                                    <a href="{{ route('accounting.payroll.periods.summary', $period) }}"
                                       class="btn btn-sm btn-outline-dark font-weight-bold px-2" title="Summary">
                                        <i class="bi bi-bar-chart-line"></i>
                                    </a>
                                    <form action="{{ route('accounting.payroll.periods.update-status', $period) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-dark font-weight-bold px-2 shadow-sm"
                                                title="Close Period"
                                                onclick="return confirm('Close this payroll period? This cannot be undone.')">
                                            <i class="bi bi-lock-fill"></i>
                                        </button>
                                    </form>

                                @elseif ($period->isClosed())
                                    <a href="{{ route('accounting.payroll.periods.records', $period) }}"
                                       class="btn btn-sm btn-light border text-dark font-weight-bold px-2" title="View Records">
                                        <i class="bi bi-list-ul"></i>
                                    </a>
                                    <a href="{{ route('accounting.payroll.periods.summary', $period) }}"
                                       class="btn btn-sm btn-outline-dark font-weight-bold px-2" title="Summary">
                                        <i class="bi bi-bar-chart-line"></i>
                                    </a>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 bg-white text-muted">
                            <span class="font-weight-bold d-block mb-1">No payroll periods yet.</span>
                            <small class="text-muted font-weight-bold text-uppercase">Create a new period to get started.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($periods->hasPages())
            <div class="px-4 py-3 bg-light border-top">
                {{ $periods->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ===== CREATE PERIOD MODAL ===== --}}
<div class="modal fade" id="createPeriodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">

            <div class="modal-header bg-white border-bottom py-3">
                <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase" id="createPeriodModalLabel">New Payroll Period</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('accounting.payroll.periods.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">

                    <div class="bg-white border rounded p-4 shadow-sm mb-4">
                        {{-- Period Type --}}
                        <div class="mb-4">
                            <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Period Type <span class="text-danger">*</span></label>
                            <select name="period_type" class="form-select shadow-sm font-weight-bold text-dark @error('period_type') is-invalid @enderror" required id="periodTypeSelect">
                                <option value="" disabled selected>Select type</option>
                                <option value="1st-15th"  {{ old('period_type') === '1st-15th'  ? 'selected' : '' }}>1st – 15th</option>
                                <option value="16th-end"  {{ old('period_type') === '16th-end'  ? 'selected' : '' }}>16th – End of Month</option>
                            </select>
                            @error('period_type')
                                <div class="invalid-feedback font-weight-bold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-4">
                            {{-- Month --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Month <span class="text-danger">*</span></label>
                                <select name="month" class="form-select shadow-sm font-weight-bold text-dark @error('month') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select month</option>
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ old('month') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('month')
                                    <div class="invalid-feedback font-weight-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Year --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Year <span class="text-danger">*</span></label>
                                <select name="year" class="form-select shadow-sm font-weight-bold text-dark @error('year') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select year</option>
                                    @foreach(range(now()->year - 1, now()->year + 1) as $y)
                                        <option value="{{ $y }}" {{ (old('year', now()->year) == $y) ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('year')
                                    <div class="invalid-feedback font-weight-bold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Coverage Preview --}}
                    <div class="mb-4">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Coverage (Auto-computed)</label>
                        <input type="text" id="coveragePreview" class="form-control bg-white shadow-sm font-weight-bold text-dark"
                               readonly placeholder="Select type, month, and year above">
                    </div>

                    {{-- Pay Date --}}
                    <div class="mb-4">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Pay Date <span class="text-danger">*</span></label>
                        <input type="date" name="pay_date"
                               class="form-control shadow-sm font-weight-bold text-dark @error('pay_date') is-invalid @enderror"
                               value="{{ old('pay_date') }}" required>
                        <div class="form-text small font-weight-bold text-uppercase mt-2">Date employees will receive their pay.</div>
                        @error('pay_date')
                            <div class="invalid-feedback font-weight-bold mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-2">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Notes <small class="text-muted fw-normal">(optional)</small></label>
                        <textarea name="notes" class="form-control shadow-sm p-3 @error('notes') is-invalid @enderror"
                                  rows="2" placeholder="e.g. Holiday adjustments included">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback font-weight-bold mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="modal-footer bg-white py-3">
                    <button type="button" class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-secondary font-weight-bold px-4 shadow-sm">Create Period</button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/**
 * Auto-compute and preview the coverage dates
 * whenever the user changes type, month, or year.
 */
(function () {
    const monthNames = [
        '', 'January','February','March','April','May','June',
        'July','August','September','October','November','December'
    ];

    function daysInMonth(month, year) {
        return new Date(year, month, 0).getDate();
    }

    function updatePreview() {
        const type  = document.querySelector('[name="period_type"]').value;
        const month = parseInt(document.querySelector('[name="month"]').value);
        const year  = parseInt(document.querySelector('[name="year"]').value);
        const preview = document.getElementById('coveragePreview');

        if (!type || !month || !year) {
            preview.value = '';
            return;
        }

        const mName = monthNames[month];

        if (type === '1st-15th') {
            preview.value = `${mName} 1 – 15, ${year}`;
        } else {
            const last = daysInMonth(month, year);
            preview.value = `${mName} 16 – ${last}, ${year}`;
        }
    }

    ['[name="period_type"]', '[name="month"]', '[name="year"]'].forEach(sel => {
        document.querySelector(sel)?.addEventListener('change', updatePreview);
    });

    // Re-open modal with old input if validation failed
    @if ($errors->any())
        const modal = new bootstrap.Modal(document.getElementById('createPeriodModal'));
        modal.show();
        updatePreview();
    @endif
})();
</script>
@endpush