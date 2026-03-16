@extends('layouts.main')

@section('title', 'My Profile')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-muted">My Profile</li>
    </ol>
@endsection

@push('styles')
<style>
    .profile-tab-nav .nav-link {
        color: #6c757d;
        border-bottom: 2px solid transparent;
        border-radius: 0;
        padding: 1rem 1.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        transition: all 0.2s;
    }
    .profile-tab-nav .nav-link.active {
        color: #1a1a1a;
        border-bottom-color: #1a1a1a;
        background: transparent;
    }
    .profile-tab-nav .nav-link:hover:not(.active) {
        color: #1a1a1a;
        background: #f8f9fa;
    }
    .field-card {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: .5rem;
        padding: 1.25rem;
        height: 100%;
        transition: border-color 0.2s;
    }
    .field-card:hover {
        border-color: #6c757d;
    }
    .field-card .field-label {
        font-size: .65rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: .5rem;
        letter-spacing: 0.5px;
    }
    .field-card .field-value {
        font-size: .95rem;
        font-weight: 700;
        color: #1a1a1a;
        word-break: break-word;
    }
    .section-heading {
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: #1a1a1a;
        border-bottom: 2px solid #f8f9fa;
        padding-bottom: .75rem;
        margin-bottom: 1.5rem;
    }
    .notification-bar {
        border: 1px solid #dee2e6;
        border-left: 4px solid #6c757d;
        border-radius: .5rem;
        background: #ffffff;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }
</style>
@endpush

@section('content')

@include('components.alerts')

{{-- ── Page Header ──────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 font-weight-bold text-dark">My Profile</h4>
        <small class="text-muted font-weight-bold text-uppercase">Manage your personal and employment information</small>
    </div>
    <button class="btn btn-secondary font-weight-bold px-4 py-2 shadow-sm"
            data-bs-toggle="modal"
            data-bs-target="#requestModal">
        <i class="bi bi-pencil-square me-2"></i>Update Info
    </button>
</div>

{{-- ── Notifications ────────────────────────────────────────────── --}}
@foreach ($pendingRequests as $req)
    <div class="notification-bar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-hourglass-split text-secondary fs-5"></i>
            <div>
                <div class="fw-bold text-dark text-uppercase small">Update Pending</div>
                <div class="text-muted small font-weight-bold">
                    {{ $req->field }} <span class="mx-1">|</span> Submitted {{ \Carbon\Carbon::parse($req->submittedDate)->format('M d, Y') }}
                </div>
            </div>
        </div>
        <span class="badge bg-light border text-dark px-3 py-2 text-uppercase">Under Review</span>
    </div>
@endforeach

@foreach ($recentApproved as $req)
    <div class="notification-bar d-flex align-items-center justify-content-between" style="border-left-color: #198754;">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div>
                <div class="fw-bold text-dark text-uppercase small">Update Approved</div>
                <div class="text-muted small font-weight-bold">
                    {{ $req->field }} <span class="mx-1">|</span> Reviewed by {{ $req->reviewedBy }}
                </div>
            </div>
        </div>
        <i class="bi bi-x-lg text-muted small" style="cursor: pointer;"></i>
    </div>
@endforeach

{{-- ── Main Profile Card ────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white p-0 border-bottom">
        <ul class="nav profile-tab-nav" id="profileTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-personal">Personal Info</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-employment">Employment</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-payroll">Payroll</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-leave">Leave Credits</a>
            </li>
        </ul>
    </div>

    <div class="card-body p-4 p-lg-5 tab-content bg-light">

        {{-- PERSONAL TAB --}}
        <div id="tab-personal" class="tab-pane fade show active">
            <div class="mb-5">
                <div class="section-heading">Basic Information</div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="field-card shadow-sm">
                            <div class="field-label">Employee ID</div>
                            <div class="field-value">{{ $user->id ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-card shadow-sm">
                            <div class="field-label">Full Name</div>
                            <div class="field-value">{{ $user->firstName }} {{ $user->middleName }} {{ $user->lastName }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-card shadow-sm">
                            <div class="field-label">Gender / Civil Status</div>
                            <div class="field-value">{{ $user->gender ?? '—' }} <span class="text-muted mx-1">|</span> {{ $user->civilStatus ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-card shadow-sm">
                            <div class="field-label">Date of Birth</div>
                            <div class="field-value">{{ $user->dateOfBirth ? \Carbon\Carbon::parse($user->dateOfBirth)->format('F d, Y') : '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-card shadow-sm">
                            <div class="field-label">Email Address</div>
                            <div class="field-value">{{ $user->email ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-card shadow-sm">
                            <div class="field-label">Phone Number</div>
                            <div class="field-value">{{ $user->phoneNumber ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="section-heading">Home Address</div>
                <div class="row g-4">
                    <div class="col-12">
                        <div class="field-card shadow-sm">
                            <div class="field-label">Complete Address</div>
                            @php
                                $fullAddress = collect([$user->addressStreet, $user->addressBarangay, $user->addressCity, $user->addressProvince, $user->addressRegion, $user->addressZipCode])->filter()->implode(', ');
                            @endphp
                            <div class="field-value">{{ $fullAddress ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- EMPLOYMENT TAB --}}
        <div id="tab-employment" class="tab-pane fade">
            <div class="section-heading">Employment Details</div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="field-card shadow-sm">
                        <div class="field-label">Position / Status</div>
                        <div class="field-value">{{ $user->position ?? '—' }} <span class="badge bg-light border text-dark ms-2">{{ ucfirst($user->employmentStatus ?? '—') }}</span></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="field-card shadow-sm">
                        <div class="field-label">Department / Branch</div>
                        <div class="field-value">{{ $user->department ?? '—' }} <span class="text-muted mx-1">|</span> {{ $user->branch ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="field-card shadow-sm">
                        <div class="field-label">Date Hired</div>
                        <div class="field-value">{{ $user->hireDate ? \Carbon\Carbon::parse($user->hireDate)->format('F d, Y') : '—' }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="field-card shadow-sm">
                        <div class="field-label">Default Shift</div>
                        <div class="field-value text-secondary">{{ $user->defaultShift ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PAYROLL TAB --}}
        <div id="tab-payroll" class="tab-pane fade">
            <div class="section-heading">Salary & Compensation</div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="field-card shadow-sm border-secondary">
                        <div class="field-label">Monthly Basic Salary</div>
                        <div class="field-value h4 mb-0 text-dark">₱ {{ $user->basicSalary ? number_format($user->basicSalary, 2) : '—' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-card shadow-sm">
                        <div class="field-label">Daily Rate</div>
                        <div class="field-value">₱ {{ $user->dailyRate ? number_format($user->dailyRate, 2) : '—' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-card shadow-sm">
                        <div class="field-label">Hourly Rate</div>
                        <div class="field-value">₱ {{ $user->hourlyRate ? number_format($user->hourlyRate, 2) : '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- LEAVE TAB --}}
        <div id="tab-leave" class="tab-pane fade">
            <div class="section-heading">Leave Credits ({{ now()->year }})</div>
            <div class="row g-4">
                @forelse($leaveBalances as $name => $balance)
                    <div class="col-md-4">
                        <div class="field-card shadow-sm text-center">
                            <div class="field-label">{{ $name }}</div>
                            <div class="field-value display-6 text-dark">{{ number_format($balance->balance, 1) }}</div>
                            <div class="text-muted font-weight-bold text-uppercase mt-2" style="font-size: 0.6rem;">Days Remaining</div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <span class="text-muted font-weight-bold text-uppercase">No leave balances found for this year.</span>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- ── Update Request Modal ────────────────────────────────────── --}}
<div class="modal fade" id="requestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <form action="{{ route('employee.profile.requests.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-white border-bottom py-3">
                    <h6 class="modal-title font-weight-bold mb-0 text-dark text-uppercase">Request Info Update</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="mb-4">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Field to Update <span class="text-danger">*</span></label>
                        <select name="field" class="form-select border-secondary shadow-sm font-weight-bold text-dark">
                            <option value="">Select a field...</option>
                            <option value="email">Email Address</option>
                            <option value="phone">Phone Number</option>
                            <option value="civilStatus">Civil Status</option>
                            <option value="address">Address</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">New Value <span class="text-danger">*</span></label>
                        <input type="text" name="new_value" class="form-control shadow-sm font-weight-bold text-dark" placeholder="Enter updated information">
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-muted small font-weight-bold text-uppercase mb-2">Reason for Update <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control shadow-sm p-3" rows="3" placeholder="Explain why this change is necessary..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-white py-3">
                    <button type="button" class="btn btn-outline-dark font-weight-bold px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-secondary font-weight-bold px-4 shadow-sm">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection