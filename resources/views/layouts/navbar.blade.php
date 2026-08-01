<div id="sidebar" class="sidebar">

    <div class="sidebar-header text-center">
        <h4 class="m-0 p-0 text-center w-100">SpinHR</h4>
    </div>

    <ul class="sidebar-menu">

        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span style="font-weight: bold; font-size: 14px;">Dashboard</span>
            </a>
        </li>

        <!-- Master Dropdown -->
        @php
            $isMasterActive = request()->routeIs('companies.*') || request()->routeIs('admin.company.*') || request()->routeIs('departments.*');
        @endphp
        <li>
            <a href="#masterSubmenu" data-bs-toggle="collapse" data-coreui-toggle="collapse" aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}" class="d-flex align-items-center justify-content-between {{ $isMasterActive ? '' : 'collapsed' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-grid-fill"></i>
                    <span style="font-weight: bold; font-size: 14px;">Master</span>
                </div>
                <i class="bi bi-chevron-down small"></i>
            </a>
            <ul class="collapse list-unstyled ps-3 mt-1 {{ $isMasterActive ? 'show' : '' }}" id="masterSubmenu">
                <li class="my-1">
                    <a href="{{ route('companies.index') }}" class="{{ request()->routeIs('companies.*') || request()->routeIs('admin.company.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i>
                        <span>Companies</span>
                    </a>
                </li>
                <li class="my-1">
                    <a href="{{ route('departments.index') }}" class="{{ request()->routeIs('departments.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i>
                        <span>Departments</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Employees Dropdown -->
        @php
            $isEmployeeActive = request()->routeIs('employees.*');
        @endphp
        <li>
            <a href="#employeesSubmenu" data-bs-toggle="collapse" data-coreui-toggle="collapse" aria-expanded="{{ $isEmployeeActive ? 'true' : 'false' }}" class="d-flex align-items-center justify-content-between {{ $isEmployeeActive ? '' : 'collapsed' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge-fill"></i>
                    <span style="font-weight: bold; font-size: 14px;">Employees</span>
                </div>
                <i class="bi bi-chevron-down small"></i>
            </a>
            <ul class="collapse list-unstyled ps-3 mt-1 {{ $isEmployeeActive ? 'show' : '' }}" id="employeesSubmenu">
                <li class="my-1">
                    <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i>
                        <span>Employees</span>
                    </a>
                </li>
            </ul>
        </li>

         <li class="my-1">
            <a href="{{ route('payroll-processing.index') }}" class="{{ request()->routeIs('payroll-processing.*') ? 'active' : '' }}">
                <i class="bi bi-kanban"></i>
                <span>Attendance & Payroll</span>
            </a>
        </li>

        {{-- <!-- Attendance Dropdown -->
        @php
            $isAttendanceActive = request()->routeIs('attendance.*') || request()->routeIs('attendance-report.*');
        @endphp
        <li>
            <a href="#attendanceSubmenu" data-bs-toggle="collapse" data-coreui-toggle="collapse" aria-expanded="{{ $isAttendanceActive ? 'true' : 'false' }}" class="d-flex align-items-center justify-content-between {{ $isAttendanceActive ? '' : 'collapsed' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-check-fill"></i>
                    <span style="font-weight: bold; font-size: 14px;">Attendance</span>
                </div>
                <i class="bi bi-chevron-down small"></i>
            </a>
            <ul class="collapse list-unstyled ps-3 mt-1 {{ $isAttendanceActive ? 'show' : '' }}" id="attendanceSubmenu">
                <li class="my-1">
                    <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i>
                        <span>Attendance</span>
                    </a>
                </li>
                <li class="my-1">
                    <a href="{{ route('attendance-report.index') }}" class="{{ request()->routeIs('attendance-report.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span> Report</span>
                    </a>
                </li>
            </ul>
        </li> --}}

        {{-- <!-- Payroll Dropdown -->
        @php
            $isPayrollActive = request()->routeIs('payroll-processing.*') || request()->routeIs('payrolls.*');
        @endphp
        <li>
            <a href="#payrollSubmenu" data-bs-toggle="collapse" data-coreui-toggle="collapse" aria-expanded="{{ $isPayrollActive ? 'true' : 'false' }}" class="d-flex align-items-center justify-content-between {{ $isPayrollActive ? '' : 'collapsed' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-wallet2"></i>
                    <span style="font-weight: bold; font-size: 14px;">Payroll</span>
                </div>
                <i class="bi bi-chevron-down small"></i>
            </a>
            <ul class="collapse list-unstyled ps-3 mt-1 {{ $isPayrollActive ? 'show' : '' }}" id="payrollSubmenu">
              
                <li class="my-1">
                    <a href="{{ route('payrolls.index') }}" class="{{ request()->routeIs('payrolls.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i>
                        <span>Salary Slip</span>
                    </a>
                </li>
            </ul> --}}
        </li>

    </ul>

</div>