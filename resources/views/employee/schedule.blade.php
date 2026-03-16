@extends('layouts.main')

@section('title', 'My Schedule')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">My Schedule</li>
    </ol>
@endsection

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 font-weight-bold text-dark">My Schedule</h4>
    
    {{-- Today Date Chip --}}
    <div class="bg-white border rounded px-3 py-2 shadow-sm d-flex align-items-center">
        <span class="h5 font-weight-bold text-dark mb-0 me-2">{{ $todayData['date']->format('d') }}</span>
        <span class="text-muted small font-weight-bold text-uppercase">{{ $todayData['date']->format('D, M Y') }}</span>
    </div>
</div>

{{-- ── Today's Schedule Card ───────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Today's Schedule</h6>
        <span class="badge bg-light border text-dark px-2 py-1">
            {{ $todayData['date']->format('l, F j, Y') }}
        </span>
    </div>
    <div class="card-body p-4">

        @if($todayData['isRestDay'])
            <div class="alert bg-light border text-muted small font-weight-bold text-uppercase text-center mb-4">
                Today is a scheduled rest day.
            </div>
        @elseif($todayData['leave'])
            <div class="alert bg-light border text-muted small font-weight-bold text-uppercase text-center mb-4">
                You are on <span class="text-dark">{{ $todayData['leave']->leaveType->name }}</span> today.
            </div>
        @endif

        {{-- Added Bootstrap 5 gutter (g-3) for consistent spacing --}}
        <div class="row g-3">
            {{-- Status Box --}}
            <div class="col-md-4">
                <div class="border rounded bg-light p-3 text-center h-100 d-flex flex-column justify-content-center py-4">
                    <span class="text-muted small font-weight-bold text-uppercase mb-2">Status</span>
                    <span class="h5 font-weight-bold text-dark mb-0">{{ $todayData['status'] }}</span>
                </div>
            </div>
            
            {{-- Time In Box --}}
            <div class="col-md-4">
                <div class="border rounded bg-light p-3 text-center h-100 d-flex flex-column justify-content-center py-4">
                    <span class="text-muted small font-weight-bold text-uppercase mb-2">Time In</span>
                    <span class="h5 font-weight-bold text-dark mb-1">
                        {{ $todayData['attendance']?->time_in 
                            ? \Carbon\Carbon::parse($todayData['attendance']->time_in)->format('h:i A') 
                            : '—' }}
                    </span>
                    <span class="text-muted" style="font-size: 0.7rem;">
                        Scheduled: {{ \Carbon\Carbon::parse($todayData['workStart'])->format('h:i A') }}
                    </span>
                </div>
            </div>

            {{-- Time Out Box --}}
            <div class="col-md-4">
                <div class="border rounded bg-light p-3 text-center h-100 d-flex flex-column justify-content-center py-4">
                    <span class="text-muted small font-weight-bold text-uppercase mb-2">Time Out</span>
                    <span class="h5 font-weight-bold text-dark mb-1">
                        {{ $todayData['attendance']?->time_out 
                            ? \Carbon\Carbon::parse($todayData['attendance']->time_out)->format('h:i A') 
                            : '—' }}
                    </span>
                    <span class="text-muted" style="font-size: 0.7rem;">
                        Scheduled: {{ \Carbon\Carbon::parse($todayData['workEnd'])->format('h:i A') }}
                    </span>
                </div>
            </div>
        </div>

        @if($todayData['holiday'])
            <div class="mt-4 text-center">
                <span class="badge bg-white border border-secondary text-dark px-3 py-2 text-uppercase font-weight-bold">
                    {{ $todayData['holiday']['name'] }} 
                    <span class="text-muted ms-1">
                        ({{ $todayData['holiday']['type'] === 'regular' ? 'Regular 200%' : 'Special 130%' }})
                    </span>
                </span>
            </div>
        @endif

    </div>
</div>

{{-- ── Monthly Summary Chips (Redesigned to Stack Vertically) ─────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="border rounded bg-white p-3 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="h3 mb-1 font-weight-bold text-dark">{{ $summary['present'] }}</span>
            <span class="text-muted small font-weight-bold text-uppercase">Present</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="border rounded bg-white p-3 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="h3 mb-1 font-weight-bold text-dark">{{ $summary['late'] }}</span>
            <span class="text-muted small font-weight-bold text-uppercase">Late</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="border rounded bg-white p-3 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="h3 mb-1 font-weight-bold text-dark">{{ $summary['absent'] }}</span>
            <span class="text-muted small font-weight-bold text-uppercase">Absent</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="border rounded bg-white p-3 shadow-sm text-center h-100 d-flex flex-column justify-content-center">
            <span class="h3 mb-1 font-weight-bold text-dark">{{ $summary['leave'] }}</span>
            <span class="text-muted small font-weight-bold text-uppercase">On Leave</span>
        </div>
    </div>
</div>

{{-- ── Calendar Card ───────────────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">
            {{ $date->format('F Y') }}
        </h6>
        <div class="d-flex align-items-center">
            <a href="{{ route('employee.schedule.index', ['month' => $prevMonth]) }}" class="btn btn-sm btn-light border text-dark px-3 text-decoration-none"><</a>
            @if($date->format('Y-m') !== now()->format('Y-m'))
                <a href="{{ route('employee.schedule.index') }}" class="btn btn-sm btn-outline-secondary mx-2 font-weight-bold text-decoration-none">Today</a>
            @else
                <div class="mx-2" style="width: 4px;"></div>
            @endif
            <a href="{{ route('employee.schedule.index', ['month' => $nextMonth]) }}" class="btn btn-sm btn-light border text-dark px-3 text-decoration-none">></a>
        </div>
    </div>
    <div class="card-body p-3">

        {{-- Day-of-week headers --}}
        <div class="row no-gutters mb-2 border-bottom pb-2">
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                <div class="col text-center text-muted small font-weight-bold text-uppercase">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        {{-- Flexbox Calendar Grid --}}
        @foreach(array_chunk($calendarCells, 7) as $week)
            <div class="row no-gutters mb-2 align-items-stretch">
                @foreach($week as $cell)
                    @php
                        $att        = $cell['attendance'];
                        $leave      = $cell['leave'];
                        $holiday    = $cell['holiday'];
                        $isOffMonth = !$cell['isCurrentMonth'];
                        $isRestDay  = $cell['isRestDay'];
                        $isToday    = $cell['isToday'];

                        if ($isOffMonth) {
                            $calStatus = 'off_month';
                            $statusLabel = '';
                        } elseif ($isRestDay) {
                            $calStatus = 'rest';
                            $statusLabel = 'Rest';
                        } elseif ($leave) {
                            $calStatus = 'leave';
                            $statusLabel = $leave->leaveType->name ?? 'Leave';
                        } elseif ($att) {
                            $calStatus = $att->status; 
                            $statusLabel = match($att->status) {
                                'present'    => 'Present',
                                'absent'     => 'Absent',
                                'late'       => 'Late',
                                'half_day'   => 'Half Day',
                                'leave'      => 'On Leave',
                                'holiday'    => 'Holiday',
                                'incomplete' => 'Incomplete',
                                default      => ucfirst($att->status),
                            };
                        } elseif ($holiday) {
                            $calStatus = 'holiday';
                            $statusLabel = 'Holiday';
                        } elseif ($cell['isCurrentMonth'] && $cell['date']->isPast()) {
                            $calStatus = 'absent';
                            $statusLabel = 'No record';
                        } else {
                            $calStatus = 'none';
                            $statusLabel = '';
                        }

                        $bgClass = in_array($calStatus, ['rest', 'off_month']) ? 'bg-light' : 'bg-white';
                        
                        $borderClass = 'border ';
                        if ($isToday) $borderClass .= 'border-secondary shadow-sm';
                        elseif ($isOffMonth) $borderClass .= 'border-light';
                        
                        $dayNumClass = 'small font-weight-bold ';
                        if ($isToday) {
                            $dayNumClass .= 'bg-secondary text-white rounded px-2 py-1';
                        } else {
                            $dayNumClass .= ($isOffMonth) ? 'text-light' : 'text-dark';
                        }

                        $pillClass = 'badge border w-100 text-truncate py-1 mb-1 fw-bold ';
                        $pillClass .= match($calStatus) {
                            'absent', 'rest', 'off_month', 'incomplete' => 'bg-light text-muted border-0',
                            'leave', 'holiday' => 'bg-white text-dark border-secondary',
                            'none' => 'd-none',
                            default => 'bg-white text-dark'
                        };
                    @endphp

                    <div class="col px-1">
                        <div class="cal-cell {{ $bgClass }} {{ $borderClass }} rounded p-2 d-flex flex-column h-100" style="min-height: 95px;">
                            
                            {{-- Day number --}}
                            <div class="text-end mb-auto">
                                <span class="{{ $dayNumClass }}">{{ $cell['date']->day }}</span>
                            </div>

                            {{-- Bottom Content --}}
                            <div class="mt-2 text-center">
                                @if($statusLabel)
                                    <span class="{{ $pillClass }}" title="{{ $statusLabel }}">{{ $statusLabel }}</span>
                                @endif

                                @if($att && $att->time_in && in_array($att->status, ['present','late','half_day','incomplete']))
                                    <div class="text-muted font-weight-bold" style="font-size: 0.65rem; line-height: 1.3;">
                                        {{ \Carbon\Carbon::parse($att->time_in)->format('H:i') }}
                                        @if($att->time_out)
                                            <br>{{ \Carbon\Carbon::parse($att->time_out)->format('H:i') }}
                                        @endif
                                    </div>
                                @endif

                                @if($holiday && !$isOffMonth)
                                    <div class="text-muted text-truncate w-100 pt-1 mt-1 border-top" style="font-size: 0.65rem;" title="{{ $holiday['name'] }}">
                                        {{ $holiday['name'] }}
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

        {{-- Legend (Fixed spacing and alignment) --}}
        <div class="d-flex flex-wrap align-items-center justify-content-center pt-3 mt-4 border-top text-muted small font-weight-bold text-uppercase gap-4">
            <div class="d-flex align-items-center">
                <span class="p-1 border border-secondary bg-secondary rounded-circle me-2"></span> 
                <span class="text-secondary">Present</span>
            </div>
            <div class="d-flex align-items-center">
                <span class="p-1 border border-secondary bg-white rounded-circle me-2"></span> 
                <span class="text-secondary">Late / Leave</span>
            </div>
            <div class="d-flex align-items-center">
                <span class="p-1 border border-light bg-light rounded-circle me-2"></span> 
                <span class="text-secondary">Absent / Rest</span>
            </div>
            <div class="d-flex align-items-center">
                <span class="border border-secondary rounded px-3 py-1 me-2"></span> 
                <span class="text-secondary">Today</span>
            </div>
        </div>

    </div>
</div>

{{-- ── Schedule & Leave Info ───────────────────────────────────────────── --}}
<div class="row g-4 mb-5">
    
    {{-- Work Schedule Card --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Work Schedule</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <tbody>
                        <tr class="border-bottom">
                            <td class="text-muted small font-weight-bold text-uppercase border-0 ps-4 py-3" style="width: 45%;">Assigned Schedule</td>
                            <td class="font-weight-bold text-dark border-0 py-3">
                                {{ $todayData['template']->name ?? 'No Schedule Assigned' }}
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="text-muted small font-weight-bold text-uppercase border-0 ps-4 py-3">Time In</td>
                            <td class="text-secondary font-weight-bold border-0 py-3">
                                {{ isset($todayData['template']) ? \Carbon\Carbon::parse($todayData['template']->shift_in)->format('h:i A') : '—' }}
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="text-muted small font-weight-bold text-uppercase border-0 ps-4 py-3">Time Out</td>
                            <td class="text-secondary font-weight-bold border-0 py-3">
                                {{ isset($todayData['template']) ? \Carbon\Carbon::parse($todayData['template']->shift_out)->format('h:i A') : '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted small font-weight-bold text-uppercase border-0 ps-4 py-3">Rest Day(s)</td>
                            <td class="text-dark font-weight-bold border-0 py-3">
                                @if(count($todayData['restDaysList']) > 0)
                                    {{ implode(', ', $todayData['restDaysList']) }}
                                @else
                                    <span class="text-muted">None configured</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Leave Balances Card --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title font-weight-bold mb-0 text-dark text-uppercase">Leave Balances ({{ date('Y') }})</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <tbody>
                        @forelse($leaveBalances as $balance)
                            <tr class="border-bottom">
                                <td class="text-muted small font-weight-bold text-uppercase border-0 ps-4 py-3" style="width: 65%;">
                                    {{ $balance->leaveType->name }}
                                </td>
                                <td class="font-weight-bold text-dark text-end border-0 pe-4 py-3">
                                    {{ number_format($balance->balance, 1) }} <span class="text-muted small ms-1">days</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-5 border-0">
                                    <span class="font-weight-bold d-block">No leave balances found for this year.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection