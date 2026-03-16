@extends('layouts.main')

@section('title', 'Timekeeping')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary">Home</a></li>
        <li class="breadcrumb-item active text-muted">Timekeeping</li>
    </ol>
@endsection

@section('content')

<x-alerts />

{{-- Test mode banner --}}
@if ($testMode)
    <div class="alert alert-light border border-secondary d-flex align-items-center justify-content-between py-2 mb-4">
        <span class="small text-dark">
            <strong>Testing Mode Active</strong> —
            Date: <strong>{{ \Carbon\Carbon::parse($testDate)->format('M d, Y') }}</strong>,
            Time: <strong>{{ \Carbon\Carbon::createFromFormat('H:i', $testTime)->format('h:i A') }}</strong>
        </span>
        <form method="POST" action="{{ route('employee.timekeeping.test-mode') }}" class="mb-0">
            @csrf
            <input type="hidden" name="action" value="disable">
            <button type="submit" class="btn btn-sm btn-secondary">Exit</button>
        </form>
    </div>
@endif

{{-- Page header --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0 font-weight-bold text-dark">Timekeeping</h4>
</div>

{{-- Live Clock --}}
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body text-center py-4">
        <h1 id="liveClock" class="font-weight-bold text-dark mb-1 display-4" style="font-variant-numeric:tabular-nums;">
            --:--:--
        </h1>
        <p id="liveDate" class="text-muted lead mb-0"></p>
    </div>
</div>

{{-- Quick Action + Calendar --}}
<div class="row mb-4">

{{-- Quick Action --}}
    <div class="col-lg-4 mb-4 mb-lg-0">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Quick Action</h6>
            </div>
            <div class="card-body p-4 d-flex flex-column">

                @if (! $isClockedIn)
                    {{-- State: Not Clocked In --}}
                    <div class="text-center mb-4 mt-2">
                        <span class="badge bg-light border text-muted px-3 py-2 text-uppercase font-weight-bold" style="letter-spacing: 1px;">
                            Currently Off The Clock
                        </span>
                    </div>

                    <form method="POST" action="{{ route('employee.timekeeping.clock-in') }}" class="mb-auto">
                        @csrf
                        @if ($testMode)
                            <div class="mb-3">
                                <label class="small text-muted font-weight-bold mb-1 d-block text-center">Test Time Override</label>
                                <input type="time" name="test_time" class="form-control text-center bg-light" value="{{ $testTime }}">
                            </div>
                        @endif
                        <button type="submit" class="btn btn-secondary btn-lg w-100 py-3 font-weight-bold shadow-sm" style="font-size: 1.1rem;">
                            CLOCK IN
                        </button>
                    </form>

                @else
                    {{-- State: Clocked In --}}
                    <div class="text-center mb-4 mt-2">
                        <span class="badge bg-white border border-secondary text-dark px-3 py-2 text-uppercase font-weight-bold mb-3" style="letter-spacing: 1px;">
                            Clocked In
                        </span>
                        <div class="mt-2">
                            <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">Elapsed Time</span>
                            <div id="elapsedTimer" class="font-weight-bold text-dark display-4" 
                                style="font-variant-numeric:tabular-nums; letter-spacing: -2px;"
                                data-clock-in="{{ $todayRecord->time_in }}"
                                data-clock-date="{{ $activeDate }}">
                                00:00:00
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('employee.timekeeping.clock-out') }}" class="mb-auto">
                        @csrf
                        @if ($testMode)
                            <div class="mb-3">
                                <label class="small text-muted font-weight-bold mb-1 d-block text-center">Test Time Override</label>
                                <input type="time" name="test_time" class="form-control text-center bg-light" value="{{ $testTime }}">
                            </div>
                        @endif
                        <button type="submit" class="btn btn-outline-dark btn-lg w-100 py-3 font-weight-bold border-2" style="font-size: 1.1rem;">
                            CLOCK OUT
                        </button>
                    </form>
                @endif


                {{-- Testing Tools Section (Pushed to bottom) --}}
                <div class="mt-4 pt-3 border-top">
                    @if (! $testMode)
                        <form method="POST" action="{{ route('employee.timekeeping.test-mode') }}" class="mb-0">
                            @csrf
                            <input type="hidden" name="action" value="enable">
                            <input type="hidden" name="test_date" value="{{ now()->toDateString() }}">
                            <input type="hidden" name="test_time" value="{{ now()->format('H:i') }}">
                            <button type="submit" class="btn btn-light border btn-sm w-100 text-muted font-weight-bold">
                                Enable Testing Mode
                            </button>
                        </form>
                    @else
                        <div class="bg-light border rounded p-3">
                            <p class="font-weight-bold text-dark mb-3 text-uppercase small text-center" style="letter-spacing: 1px;">Testing Tools</p>

                            <div class="mb-3">
                                <label class="small text-muted font-weight-bold mb-1 d-block">Selected Date</label>
                                <div id="debugDateDisplay" class="form-control form-control-sm bg-white font-weight-bold text-center border-secondary" readonly>
                                    {{ \Carbon\Carbon::parse($testDate)->format('D, M d, Y') }}
                                </div>
                            </div>

                            <div class="d-flex" style="gap: 0.5rem;">
                                <form method="POST" action="{{ route('employee.timekeeping.delete-attendance') }}" id="debugDeleteForm" onsubmit="return confirm('Delete attendance?')" class="flex-fill mb-0">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="date" id="debugDeleteDateInput" value="{{ $testDate }}">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm w-100 font-weight-bold">
                                        Delete Auth
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('employee.timekeeping.test-mode') }}" class="flex-fill mb-0">
                                    @csrf
                                    <input type="hidden" name="action" value="disable">
                                    <button type="submit" class="btn btn-dark btn-sm w-100 font-weight-bold">
                                        Exit Test
                                    </button>
                                </form>
                            </div>

                            {{-- Hidden Sync Form --}}
                            <form method="POST" action="{{ route('employee.timekeeping.test-mode') }}" id="debugDateSyncForm" class="d-none">
                                @csrf
                                <input type="hidden" name="action" value="enable">
                                <input type="hidden" name="test_date" id="debugTestDateInput" value="{{ $testDate }}">
                                <input type="hidden" name="test_time" value="{{ $testTime }}">
                            </form>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Attendance Calendar --}}
    <div class="col-lg-8">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Attendance Calendar</h6>
                <div class="d-flex align-items-center">
                    <a href="{{ $calPrevHref }}" class="btn btn-sm btn-light border text-dark px-3"><</a>
                    <span class="font-weight-bold text-dark text-center" style="min-width:140px;">
                        {{ $calendarLabel }}
                    </span>
                    <a href="{{ $calNextHref }}" class="btn btn-sm btn-light border text-dark px-3">></a>
                </div>
            </div>
            <div class="card-body p-3">

                {{-- Day-of-week headers --}}
                <div class="row no-gutters mb-2 border-bottom pb-2">
                    @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                        <div class="col text-center text-muted small font-weight-bold text-uppercase">
                            {{ $d }}
                        </div>
                    @endforeach
                </div>

                {{-- Calendar rows --}}
                @foreach (array_chunk($calendarDays, 7) as $week)
                    {{-- align-items-stretch ensures all columns in the row are identical in height --}}
                    <div class="row no-gutters mb-2 align-items-stretch">
                        @foreach ($week as $cell)
                            <div class="col px-1">
                                @if ($cell === null)
                                    <div class="h-100" style="min-height:90px;"></div>
                                @else
                                    @php
                                        $att    = $cell['attendance'];
                                        $hasAtt = $att && $att->time_in;

                                        if ($cell['is_rest_day']) {
                                            $calStatus = 'rest';
                                        } elseif ($cell['is_leave']) {
                                            $calStatus = 'leave';
                                        } elseif ($hasAtt) {
                                            $calStatus = $att->time_out ? $att->status : 'ongoing';
                                        } elseif ($cell['holiday']) {
                                            $calStatus = 'holiday';
                                        } else {
                                            $calStatus = 'none';
                                        }

                                        $calLabel = match($calStatus) {
                                            'present'    => 'Present',
                                            'ongoing'    => 'Ongoing',
                                            'late'       => 'Late',
                                            'half_day'   => 'Half Day',
                                            'absent'     => 'Absent',
                                            'incomplete' => 'Incomplete',
                                            'leave'      => 'On Leave',
                                            'holiday'    => 'Holiday',
                                            'rest'       => 'Rest',
                                            default      => '',
                                        };

                                        $isToday     = $cell['is_today'];
                                        $isDebugDate = isset($testDate) && $cell['date'] === $testDate;
                                        $isPast      = $cell['is_past'] ?? ($cell['date'] < now()->toDateString());

                                        $bgClass = ($calStatus === 'rest') ? 'bg-light' : 'bg-white';
                                        
                                        $borderClass = 'border ';
                                        if ($isToday) $borderClass .= 'border-secondary shadow-sm';
                                        elseif ($isDebugDate) $borderClass .= 'border-dark';
                                        
                                        $dayNumClass = 'small font-weight-bold ';
                                        if ($isToday) {
                                            $dayNumClass .= 'bg-secondary text-white rounded px-2 py-1';
                                        } else {
                                            $dayNumClass .= ($isPast && $calStatus === 'none') ? 'text-muted' : 'text-dark';
                                        }

                                        $pillClass = 'badge border w-100 text-truncate ';
                                        $pillClass .= match($calStatus) {
                                            'absent', 'rest', 'incomplete' => 'bg-light text-muted border-0',
                                            'none' => 'd-none',
                                            default => 'bg-white text-dark'
                                        };
                                    @endphp

                                    {{-- h-100 + flex-column structure guarantees all cards expand evenly regardless of data --}}
                                    <div class="debug-cal-cell {{ $bgClass }} {{ $borderClass }} rounded p-2 d-flex flex-column h-100"
                                         data-date="{{ $cell['date'] }}"
                                         data-label="{{ \Carbon\Carbon::parse($cell['date'])->format('D, M d, Y') }}"
                                         onclick="selectDebugDate(this)"
                                         style="min-height:90px; cursor:pointer;">

                                        {{-- Top: Day number --}}
                                        <div class="text-right mb-auto">
                                            <span class="{{ $dayNumClass }}">{{ $cell['day'] }}</span>
                                        </div>

                                        {{-- Bottom: Status and Time --}}
                                        <div class="mt-2 text-center">
                                            @if($calLabel && $calStatus !== 'none')
                                                <span class="{{ $pillClass }} py-1 mb-1">{{ $calLabel }}</span>
                                            @endif

                                            @if($hasAtt && $att->time_in)
                                                <div class="text-muted font-weight-bold" style="font-size: 0.65rem; line-height: 1.3;">
                                                    {{ \Carbon\Carbon::parse($att->time_in)->format('H:i') }}
                                                    @if($att->time_out)
                                                        <br>{{ \Carbon\Carbon::parse($att->time_out)->format('H:i') }}
                                                    @endif
                                                </div>
                                            @endif

                                            @if($cell['holiday'])
                                                <div class="text-muted text-truncate w-100" style="font-size: 0.65rem;" title="{{ $cell['holiday']['name'] }}">
                                                    {{ $cell['holiday']['name'] }}
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- Quick Stats --}}
<div class="row mb-4">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <p class="text-muted small font-weight-bold text-uppercase mb-1">This Week</p>
                <h3 class="mb-0 font-weight-bold text-dark">{{ $stats['week_hours'] }} <span class="h6 text-muted">hrs</span></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <p class="text-muted small font-weight-bold text-uppercase mb-1">This Month</p>
                <h3 class="mb-0 font-weight-bold text-dark">{{ $stats['month_hours'] }} <span class="h6 text-muted">hrs</span></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <p class="text-muted small font-weight-bold text-uppercase mb-1">Days Present (Month)</p>
                <h3 class="mb-0 font-weight-bold text-dark">{{ $stats['days_present'] }} <span class="h6 text-muted">/ {{ $stats['work_days'] }}</span></h3>
            </div>
        </div>
    </div>
</div>

{{-- DTR Table --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Daily Time Record</h6>
    </div>
    <div class="card-body">

        {{-- Filters --}}
        <form method="GET" action="{{ route('employee.timekeeping.index') }}" class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <label class="small text-muted font-weight-bold mb-2 d-block">Month</label>
                <select name="month" class="form-control" onchange="this.form.submit()">
                    @foreach ([
                        1 => 'January', 2 => 'February', 3 => 'March',
                        4 => 'April', 5 => 'May', 6 => 'June',
                        7 => 'July', 8 => 'August', 9 => 'September',
                        10 => 'October', 11 => 'November', 12 => 'December',
                    ] as $num => $name)
                        <option value="{{ $num }}" @selected($filters['month'] == $num)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <label class="small text-muted font-weight-bold mb-2 d-block">Year</label>
                <select name="year" class="form-control" onchange="this.form.submit()">
                    @foreach ($availableYears as $yr)
                        <option value="{{ $yr }}" @selected($filters['year'] == $yr)>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="small text-muted font-weight-bold mb-2 d-block">Cutoff Period</label>
                <select name="cutoff" class="form-control" onchange="this.form.submit()">
                    <option value="full"   @selected($filters['cutoff'] === 'full')>Full Month</option>
                    <option value="first"  @selected($filters['cutoff'] === 'first')>1st – 15th</option>
                    <option value="second" @selected($filters['cutoff'] === 'second')>16th – End</option>
                </select>
            </div>
        </form>

        {{-- Table --}}
        @if ($records->isEmpty())
            <div class="text-center text-muted py-5 bg-light rounded">
                <p class="lead mb-0 font-weight-bold">No attendance records found for this period.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="border-0 font-weight-bold">Date</th>
                            <th class="border-0 font-weight-bold">Time In</th>
                            <th class="border-0 font-weight-bold">Time Out</th>
                            <th class="border-0 font-weight-bold">Hours</th>
                            <th class="border-0 font-weight-bold text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $record)
                            @php
                                $isOngoing = $record->time_in && ! $record->time_out;
                            @endphp
                            <tr class="border-bottom">
                                <td class="text-nowrap font-weight-bold text-dark">
                                    {{ $record->date->format('M d, Y') }} <span class="text-muted small ml-1">{{ $record->date->format('D') }}</span>
                                </td>
                                <td class="text-nowrap text-secondary font-weight-bold">
                                    {{ $record->time_in ? \Carbon\Carbon::parse($record->time_in)->format('h:i A') : '—' }}
                                </td>
                                <td class="text-nowrap text-secondary font-weight-bold">
                                    {{ $record->time_out ? \Carbon\Carbon::parse($record->time_out)->format('h:i A') : '—' }}
                                </td>
                                <td class="text-nowrap font-weight-bold">
                                    @if ($isOngoing)
                                        <span class="text-secondary small">Ongoing…</span>
                                    @elseif ($record->time_out)
                                        {{ number_format($record->hours_worked, 2) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <span class="badge bg-light border text-dark px-2 py-1">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $records->links() }}
            </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
/* ── Live clock ─────────────────────────────────────────────────────────── */
(function () {
    function tick() {
        const now = new Date();
        document.getElementById('liveClock').textContent =
            now.toLocaleTimeString('en-US', { hour12: false });
        document.getElementById('liveDate').textContent =
            now.toLocaleDateString('en-US', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
            });
    }
    tick();
    setInterval(tick, 1000);
})();

/* ── Elapsed timer ──────────────────────────────────────────────────────── */
(function () {
    const el = document.getElementById('elapsedTimer');
    if (!el) return;

    const timeIn  = el.dataset.clockIn;
    const dateStr = el.dataset.clockDate;
    if (!timeIn || !dateStr) return;

    const base = new Date(dateStr + 'T' + timeIn);

    function pad(n) { return String(n).padStart(2, '0'); }
    function tick() {
        const diff = Math.max(0, Math.floor((Date.now() - base.getTime()) / 1000));
        el.textContent =
            pad(Math.floor(diff / 3600))        + ':' +
            pad(Math.floor((diff % 3600) / 60)) + ':' +
            pad(diff % 60);
    }
    tick();
    setInterval(tick, 1000);
})();

/* ── Debug calendar date picker ─────────────────────────────────────────── */
function selectDebugDate(el) {
    @if ($testMode)
    const dateVal   = el.dataset.date;
    const dateLabel = el.dataset.label;

    const testInput   = document.getElementById('debugTestDateInput');
    const deleteInput = document.getElementById('debugDeleteDateInput');
    if (testInput)   testInput.value   = dateVal;
    if (deleteInput) deleteInput.value = dateVal;

    const display = document.getElementById('debugDateDisplay');
    if (display) display.textContent = dateLabel;

    document.querySelectorAll('.debug-cal-cell').forEach(function (c) {
        c.classList.remove('border-dark');
    });
    el.classList.add('border-dark');

    const syncForm = document.getElementById('debugDateSyncForm');
    if (syncForm) syncForm.submit();
    @endif
}
</script>
@endpush