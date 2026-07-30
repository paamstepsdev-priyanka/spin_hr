<?php

namespace App\Http\Controllers;

use App\Models\AttendanceMonth;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\CompanyScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with cached company-scoped metrics.
     */
    public function index(): View
    {
        $companyId = CompanyScope::id();
        $cacheKey = 'dashboard_stats_' . ($companyId ?? 'all');

        $stats = Cache::remember($cacheKey, 60, function () {
            return [
                'totalEmployees' => Employee::forCurrentCompany()->count(),
                'activeEmployees' => Employee::forCurrentCompany()->where('status', 'active')->count(),
                'totalBranches' => Branch::forCurrentCompany()->count(),
                'totalDepartments' => Department::forCurrentCompany()->count(),
                'attendanceBatches' => AttendanceMonth::forCurrentCompany()->count(),
                'payrollBatches' => Payroll::forCurrentCompany()->count(),
                'totalNetSalary' => Payroll::forCurrentCompany()->sum('total_net_salary'),
            ];
        });

        $currentCompany = CompanyScope::currentCompany();

        return view('Admin.Dashboard.index', compact('stats', 'currentCompany'));
    }
}
