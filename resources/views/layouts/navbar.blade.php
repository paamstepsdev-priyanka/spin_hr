<div id="sidebar" class="sidebar">

    <div class="sidebar-header text-center">
        <h4 class="m-0 p-0 text-center w-100">SpinHR</h4>
    </div>

    <ul class="sidebar-menu">

        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="menu-title">Master</li>

        <li>
            <a href="{{ route('companies.index') }}" class="{{ request()->routeIs('companies.*') || request()->routeIs('admin.company.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Companies</span>
            </a>
        </li>

        <li>
            <a href="{{ route('departments.index') }}" class="{{ request()->routeIs('departments.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i>
                <span>Departments</span>
            </a>
        </li>

        <li>
            <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Employees</span>
            </a>
        </li>

        <li>
            <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Attendance</span>
            </a>
        </li>


        {{-- <li>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Users</span>
            </a>
        </li> --}}

    </ul>

</div>