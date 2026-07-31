<div id="sidebar" class="sidebar">

    <!-- Top Sidebar Brand / Header -->
    <div class="sidebar-header text-center py-3 border-bottom border-light border-opacity-10">
        <h4 class="m-0 p-0 text-center w-100 text-white fw-bold">SpinHR ESS</h4>
    </div>


    <!-- Employee Navigation Menu -->
    <ul class="sidebar-menu mt-2">

        <li>
            <a href="{{ route('employee.dashboard') }}" class="{{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span class="fw-bold" style="font-size: 14px;">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="{{ route('employee.profile') }}" class="{{ request()->routeIs('employee.profile') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i>
                <span class="fw-bold" style="font-size: 14px;">My Profile</span>
            </a>
        </li>

        <li>
            <a href="{{ route('employee.attendance') }}" class="{{ request()->routeIs('employee.attendance') ? 'active' : '' }}">
                <i class="bi bi-calendar-check-fill"></i>
                <span class="fw-bold" style="font-size: 14px;">My Attendance</span>
            </a>
        </li>

        <li>
            <a href="{{ route('employee.salary-history') }}" class="{{ request()->routeIs('employee.salary-history') ? 'active' : '' }}">
                <i class="bi bi-currency-rupee"></i>
                <span class="fw-bold" style="font-size: 14px;">Salary History</span>
            </a>
        </li>

        <li>
            <a href="{{ route('employee.payroll-history') }}" class="{{ request()->routeIs('employee.payroll-history') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span class="fw-bold" style="font-size: 14px;">Payroll History</span>
            </a>
        </li>

        <li>
            <a href="{{ route('employee.payslips.index') }}" class="{{ request()->routeIs('employee.payslips.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text-fill"></i>
                <span class="fw-bold" style="font-size: 14px;">My Payslips</span>
            </a>
        </li>

        {{-- <li>
            <a href="{{ route('employee.documents') }}" class="{{ request()->routeIs('employee.documents') ? 'active' : '' }}">
                <i class="bi bi-folder-symlink-fill"></i>
                <span class="fw-bold" style="font-size: 14px;">My Documents</span>
            </a>
        </li> --}}


        <li class="mt-3 pt-2 border-top border-light border-opacity-10">
            <form action="{{ route('logout') }}" method="POST" id="sidebar-logout-form" class="d-none">
                @csrf
            </form>
            <a href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();" class="text-white-50">
                <i class="bi bi-box-arrow-right text-danger"></i>
                <span class="fw-bold text-white" style="font-size: 14px;">Logout</span>
            </a>
        </li>

    </ul>

</div>
