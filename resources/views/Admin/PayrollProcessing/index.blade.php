@extends('layouts.admin')

@section('title', 'Payroll Processing')

@section('content')
<div class="container-fluid px-0">

    <!-- Header Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="bi bi-calendar-range fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-dark">Payroll Processing</h4>
                        <p class="text-secondary mb-0 small">Track attendance recording and salary processing for each month.</p>
                    </div>
                </div>

                <!-- Financial Year Selector -->
                <div class="d-flex align-items-center gap-2 align-self-start align-self-md-center">
                    <a href="{{ route('payroll-processing.index', ['fy' => $prevFyYear]) }}" class="btn btn-outline-secondary btn-sm px-2 py-1" title="Previous Financial Year">
                        <i class="bi bi-chevron-left"></i>
                    </a>

                    <div class="input-group input-group-sm" style="min-width: 150px;">
                        <span class="input-group-text bg-body-tertiary border-end-0 text-secondary">
                            <i class="bi bi-calendar3"></i>
                        </span>
                        <select class="form-select form-select-sm border-start-0 fw-semibold text-dark" onchange="window.location.href='{{ route('payroll-processing.index') }}?fy=' + this.value">
                            @foreach($availableFys as $yVal => $label)
                                <option value="{{ $yVal }}" {{ $yVal == $startYear ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <a href="{{ route('payroll-processing.index', ['fy' => $nextFyYear]) }}" class="btn btn-outline-secondary btn-sm px-2 py-1" title="Next Financial Year">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Top KPI Summary Cards -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <!-- Card 1: Attendance Completed -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">Attendance Completed</div>
                        <div class="fs-4 fw-bold text-dark">
                            <span class="text-success">{{ $attCompletedCount }}</span> <span class="text-secondary fs-6">/ 12</span>
                        </div>
                        <div class="small text-muted" style="font-size: 11px;">Months</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Salary Processed -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">Salary Processed</div>
                        <div class="fs-4 fw-bold text-dark">
                            <span class="text-primary">{{ $payrollProcessedCount }}</span> <span class="text-secondary fs-6">/ 12</span>
                        </div>
                        <div class="small text-muted" style="font-size: 11px;">Months</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: In Progress -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">In Progress</div>
                        <div class="fs-4 fw-bold text-dark">
                            <span class="text-warning">{{ $inProgressCount }}</span> <span class="text-secondary fs-6">/ 12</span>
                        </div>
                        <div class="small text-muted" style="font-size: 11px;">Months</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Payslip Generated -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-file-earmark-text fs-4"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">Payslip Generated</div>
                        <div class="fs-4 fw-bold text-dark">
                            <span class="text-info">{{ $payslipGeneratedCount }}</span> <span class="text-secondary fs-6">/ 12</span>
                        </div>
                        <div class="small text-muted" style="font-size: 11px;">Months</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $activeAndPastMonths = array_filter($processedMonths, fn($m) => !$m['is_future']);
        $futureMonths = array_filter($processedMonths, fn($m) => $m['is_future']);
    @endphp

    <!-- Past Months & Current Month Grid -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            
            <!-- Section 1: Past & Active Months -->
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="fw-bold text-secondary small text-uppercase tracking-wider">Past & Active Months</span>
                <div class="flex-grow-1 border-bottom"></div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 mb-4">
                @foreach($activeAndPastMonths as $m)
                    <div class="col">
                        <div class="card h-100 {{ $m['is_current'] ? 'border border-warning shadow' : 'border border-light shadow-sm' }}">
                            <div class="card-header bg-transparent border-0 pt-3 px-3 pb-0 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    @if($m['is_complete'])
                                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    @elseif($m['is_current'])
                                        <i class="bi bi-clock-history text-warning fs-5"></i>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-warning fs-5"></i>
                                    @endif
                                    <span class="fw-bold text-dark">{{ $m['short_name'] }}</span>
                                    <span class="text-secondary small">{{ $m['year'] }}</span>
                                </div>
                                @if($m['is_current'])
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">Current</span>
                                @endif
                            </div>
                            <div class="card-body p-3 small">
                                <!-- Step 1: Attendance (Always shown) -->
                                @php
                                    if (!$m['has_attendance']) {
                                        $attUrl = route('attendance.create', ['month' => $m['month'], 'year' => $m['year']]);
                                    } elseif ($m['attendance_locked']) {
                                        $attUrl = $m['att_record'] ? route('attendance.show', $m['att_record']->id) : route('attendance.index', ['month' => $m['month'], 'year' => $m['year']]);
                                    } else {
                                        $attUrl = $m['att_record'] ? route('attendance.edit', $m['att_record']->id) : route('attendance.index', ['month' => $m['month'], 'year' => $m['year']]);
                                    }
                                @endphp
                                <a href="{{ $attUrl }}" class="d-flex justify-content-between align-items-center py-1 text-decoration-none text-reset {{ $m['has_attendance'] ? 'border-bottom border-light' : '' }}" style="cursor: pointer;" title="Click to open Attendance">
                                    <span class="text-secondary"><i class="bi bi-people me-1 text-muted"></i>Attendance</span>
                                    @if($m['attendance_locked'])
                                        <span class="fw-semibold text-secondary"><i class="bi bi-lock-fill me-1"></i>Locked</span>
                                    @elseif($m['has_attendance'])
                                        <span class="fw-semibold text-success">Completed</span>
                                    @else
                                        <span class="fw-semibold text-warning">Pending</span>
                                    @endif
                                </a>

                                <!-- Step 2: Salary (Shown ONLY IF Attendance is Completed) -->
                                @if($m['has_attendance'])
                                    @php
                                        if (!$m['has_payroll']) {
                                            $payUrl = $m['attendance_locked'] ? route('payrolls.create', ['month' => $m['month'], 'year' => $m['year']]) : '#';
                                        } else {
                                            $payUrl = $m['pay_record'] ? route('payrolls.show', $m['pay_record']->id) : route('payroll-processing.show', [$m['year'], $m['month']]);
                                        }
                                    @endphp
                                    <a href="{{ $payUrl }}" class="d-flex justify-content-between align-items-center py-1 text-decoration-none text-reset {{ $m['has_payroll'] ? 'border-bottom border-light' : '' }}" style="cursor: pointer;" title="{{ !$m['has_payroll'] && !$m['attendance_locked'] ? 'Complete attendance for all company branches before generating payroll.' : 'Click to open Salary / Payroll' }}">
                                        <span class="text-secondary"><i class="bi bi-wallet2 me-1 text-muted"></i>Salary</span>
                                        <span class="fw-semibold {{ $m['has_payroll'] ? 'text-success' : 'text-warning' }}">
                                            {{ $m['pay_status'] }}
                                        </span>
                                    </a>
                                @endif

                                <!-- Step 3: Payslip (Shown ONLY IF Payroll is Generated) -->
                                @if($m['has_payroll'])
                                    @php
                                        $payslipUrl = route('payroll-processing.show', [$m['year'], $m['month']]);
                                    @endphp
                                    <a href="{{ $payslipUrl }}" class="d-flex justify-content-between align-items-center py-1 text-decoration-none text-reset" style="cursor: pointer;" title="Click to open Payslips">
                                        <span class="text-secondary"><i class="bi bi-file-earmark-text me-1 text-muted"></i>Payslip</span>
                                        <span class="fw-semibold {{ $m['has_payslip'] ? 'text-info' : 'text-warning' }}">
                                            {{ $m['payslip_status'] }}
                                        </span>
                                    </a>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent border-0 p-3 pt-0">
                                @if(!$m['has_attendance'])
                                    <a href="{{ route('attendance.create', ['month' => $m['month'], 'year' => $m['year']]) }}" class="btn {{ $m['is_current'] ? 'btn-outline-warning' : 'btn-outline-primary' }} btn-sm w-100 rounded-pill d-flex align-items-center justify-content-center gap-1 fw-semibold">
                                        <i class="bi bi-plus-circle"></i> Generate Attendance
                                    </a>
                                @elseif(!$m['has_payroll'])
                                    @if($m['attendance_locked'])
                                        <a href="{{ route('payrolls.create', ['month' => $m['month'], 'year' => $m['year']]) }}" class="btn {{ $m['is_current'] ? 'btn-outline-warning' : 'btn-outline-primary' }} btn-sm w-100 rounded-pill d-flex align-items-center justify-content-center gap-1 fw-semibold">
                                            <i class="bi bi-currency-rupee"></i> Generate Payroll
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 rounded-pill d-flex align-items-center justify-content-center gap-1 fw-semibold opacity-75" disabled title="Complete attendance for all company branches before generating payroll.">
                                            <i class="bi bi-currency-rupee"></i> Generate Payroll
                                        </button>
                                    @endif
                                @elseif(!$m['has_payslip'])
                                    <a href="{{ route('payroll-processing.show', [$m['year'], $m['month']]) }}" class="btn btn-outline-info btn-sm w-100 rounded-pill d-flex align-items-center justify-content-center gap-1 fw-semibold">
                                        <i class="bi bi-file-earmark-plus"></i> Generate Payslip
                                    </a>
                                @else
                                    <a href="{{ route('payroll-processing.show', [$m['year'], $m['month']]) }}" class="btn {{ $m['is_current'] ? 'btn-outline-warning' : 'btn-outline-primary' }} btn-sm w-100 rounded-pill d-flex align-items-center justify-content-center gap-1 fw-semibold">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Section 2: Future Months (Locked) -->
            @if(count($futureMonths) > 0)
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-lock text-secondary"></i>
                    <span class="fw-bold text-secondary small text-uppercase tracking-wider">Future Months (Opens as per schedule)</span>
                    <div class="flex-grow-1 border-bottom"></div>
                </div>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                    @foreach($futureMonths as $m)
                        <div class="col">
                            <div class="card h-100 bg-body-tertiary border-0 opacity-75">
                                <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
                                    <div class="mb-2 text-secondary">
                                        <i class="bi bi-lock fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold text-secondary mb-1">{{ $m['short_name'] }} {{ $m['year'] }}</h6>
                                    <div class="small text-muted mb-3" style="font-size: 12px;">
                                        Opens on<br><strong>1 {{ $m['month_name'] }} {{ $m['year'] }}</strong>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary w-100 rounded-pill disabled" disabled>
                                        Locked
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

        <!-- Legend Footer -->
        <div class="card-footer bg-body-tertiary border-top p-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 small text-secondary">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Completed</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-clock-history text-primary"></i>
                        <span>In Progress</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-exclamation-circle-fill text-warning"></i>
                        <span>Pending</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-file-earmark-check-fill text-info"></i>
                        <span>Generated</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-lock-fill text-secondary"></i>
                        <span>Locked / Future</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="fw-bold text-muted">-</span>
                        <span>Not Applicable</span>
                    </div>
                </div>
                <div class="text-muted fst-italic">
                    <i class="bi bi-info-circle me-1"></i> Click on a month to view details
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
