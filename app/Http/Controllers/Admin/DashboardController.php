<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceMonth;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\CompanyScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the HRMS admin dashboard with cached company-scoped statistics.
     */
    public function index(): View
    {
        $companyId = CompanyScope::id();
        $isAllCompanies = ($companyId === null);
        $cacheKey = 'dashboard_stats_' . ($companyId ?? 'all');
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $firstDate = $now->copy()->startOfMonth()->toDateString();
        $lastDate = $now->copy()->endOfMonth()->toDateString();

        $stats = Cache::remember($cacheKey, 60, function () use ($companyId, $isAllCompanies, $currentMonth, $currentYear, $firstDate, $lastDate) {
            // Active Companies Count
            if ($isAllCompanies) {
                $totalCompanies = Company::where('status', 'active')->count();
            } else {
                $totalCompanies = 1;
            }

            // KPI 1: Active Employees
            $totalActiveEmployees = Employee::forCurrentCompany()->where('status', 'active')->count();

            // KPI 2: Departments
            $totalDepartments = Department::forCurrentCompany()->count();

            // KPI 3: Branches
            $totalBranches = Branch::forCurrentCompany()->count();

            // KPI 4 & 5: Attendance and Payroll Status (Company-wise progress)
            if ($isAllCompanies) {
                // All Companies mode: Count companies with attendance/payroll generated for current month
                $attendanceCompletedCompanies = Company::where('status', 'active')
                    ->whereHas('attendanceMonths', function ($q) use ($currentMonth, $currentYear) {
                        $q->where('month', $currentMonth)->where('year', $currentYear);
                    })
                    ->count();

                $attendancePendingCompanies = max(0, $totalCompanies - $attendanceCompletedCompanies);

                $attendanceCardStat = "{$attendanceCompletedCompanies} / {$totalCompanies}";
                $attendanceStatusText = "{$attendanceCompletedCompanies} / {$totalCompanies} Companies";

                if ($totalCompanies > 0 && $attendanceCompletedCompanies === $totalCompanies) {
                    $attendanceBadgeClass = 'bg-success';
                    $attendanceBadgeText = 'All Completed';
                } elseif ($attendanceCompletedCompanies > 0) {
                    $attendanceBadgeClass = 'bg-warning text-dark';
                    $attendanceBadgeText = "{$attendanceCompletedCompanies}/{$totalCompanies} Completed";
                } else {
                    $attendanceBadgeClass = 'bg-danger';
                    $attendanceBadgeText = '0 Completed';
                }

                $payrollCompletedCompanies = Company::where('status', 'active')
                    ->whereHas('payrolls', function ($q) use ($currentMonth, $currentYear) {
                        $q->where('month', $currentMonth)->where('year', $currentYear);
                    })
                    ->count();

                $payrollPendingCompanies = max(0, $totalCompanies - $payrollCompletedCompanies);

                $payrollCardStat = "{$payrollCompletedCompanies} / {$totalCompanies}";
                $payrollStatusText = "{$payrollCompletedCompanies} / {$totalCompanies} Companies";

                if ($totalCompanies > 0 && $payrollCompletedCompanies === $totalCompanies) {
                    $payrollBadgeClass = 'bg-success';
                    $payrollBadgeText = 'All Generated';
                } elseif ($payrollCompletedCompanies > 0) {
                    $payrollBadgeClass = 'bg-warning text-dark';
                    $payrollBadgeText = "{$payrollCompletedCompanies}/{$totalCompanies} Generated";
                } else {
                    $payrollBadgeClass = 'bg-danger';
                    $payrollBadgeText = '0 Generated';
                }

                $monthlySalary = (float) Payroll::forCurrentCompany()
                    ->where('month', $currentMonth)
                    ->where('year', $currentYear)
                    ->sum('total_net_salary');
            } else {
                // Single Company mode
                $hasAttendance = AttendanceMonth::where('company_id', $companyId)
                    ->where('month', $currentMonth)
                    ->where('year', $currentYear)
                    ->exists();

                if ($hasAttendance) {
                    $attendanceCardStat = 'Completed';
                    $attendanceStatusText = 'Completed';
                    $attendanceBadgeText = 'Completed';
                    $attendanceBadgeClass = 'bg-success';
                    $attendanceCompletedCompanies = 1;
                    $attendancePendingCompanies = 0;
                } else {
                    $attendanceCardStat = 'Pending';
                    $attendanceStatusText = 'Pending';
                    $attendanceBadgeText = 'Pending';
                    $attendanceBadgeClass = 'bg-warning text-dark';
                    $attendanceCompletedCompanies = 0;
                    $attendancePendingCompanies = 1;
                }

                $currentMonthPayrolls = Payroll::where('company_id', $companyId)
                    ->where('month', $currentMonth)
                    ->where('year', $currentYear)
                    ->get();

                if ($currentMonthPayrolls->isNotEmpty()) {
                    $statuses = $currentMonthPayrolls->pluck('status')->unique();
                    if ($statuses->contains('Paid')) {
                        $payrollCardStat = 'Paid';
                        $payrollStatusText = 'Paid';
                        $payrollBadgeText = 'Paid';
                        $payrollBadgeClass = 'bg-info text-dark';
                    } elseif ($statuses->contains('Locked')) {
                        $payrollCardStat = 'Locked';
                        $payrollStatusText = 'Locked';
                        $payrollBadgeText = 'Locked';
                        $payrollBadgeClass = 'bg-warning text-dark';
                    } else {
                        $payrollCardStat = 'Generated';
                        $payrollStatusText = 'Generated';
                        $payrollBadgeText = 'Generated';
                        $payrollBadgeClass = 'bg-success';
                    }
                    $payrollCompletedCompanies = 1;
                    $payrollPendingCompanies = 0;
                    $monthlySalary = (float) $currentMonthPayrolls->sum('total_net_salary');
                } else {
                    $payrollCardStat = 'Pending';
                    $payrollStatusText = 'Pending';
                    $payrollBadgeText = 'Pending';
                    $payrollBadgeClass = 'bg-warning text-dark';
                    $payrollCompletedCompanies = 0;
                    $payrollPendingCompanies = 1;
                    $monthlySalary = 0.00;
                }
            }

            // Row 2: Employee Distribution by Department
            $departmentDistribution = Department::forCurrentCompany()
                ->withCount(['employees' => function ($q) {
                    $q->forCurrentCompany()->where('status', 'active');
                }])
                ->orderBy('name', 'asc')
                ->get()
                ->map(function ($dept) {
                    return [
                        'name' => $dept->name,
                        'count' => $dept->employees_count,
                    ];
                });

            // Quick Summary metrics
            $employeesMissingSalary = Employee::forCurrentCompany()
                ->where('status', 'active')
                ->whereDoesntHave('salaries', function ($q) use ($firstDate, $lastDate) {
                    $q->where('status', 'active')
                        ->where('effective_from', '<=', $lastDate)
                        ->where(function ($sub) use ($firstDate) {
                            $sub->whereNull('effective_to')
                                ->orWhere('effective_to', '>=', $firstDate);
                        });
                })
                ->count();

            if ($isAllCompanies) {
                $attendanceMonthIds = AttendanceMonth::forCurrentCompany()
                    ->where('month', $currentMonth)
                    ->where('year', $currentYear)
                    ->pluck('id');

                $employeesMissingAttendance = Employee::forCurrentCompany()
                    ->where('status', 'active')
                    ->whereDoesntHave('monthlyAttendanceDetails', function ($q) use ($attendanceMonthIds) {
                        $q->whereIn('attendance_month_id', $attendanceMonthIds);
                    })
                    ->count();
            } else {
                if ($attendanceCompletedCompanies > 0) {
                    $attendanceMonthIds = AttendanceMonth::where('company_id', $companyId)
                        ->where('month', $currentMonth)
                        ->where('year', $currentYear)
                        ->pluck('id');

                    $employeesMissingAttendance = Employee::where('company_id', $companyId)
                        ->where('status', 'active')
                        ->whereDoesntHave('monthlyAttendanceDetails', function ($q) use ($attendanceMonthIds) {
                            $q->whereIn('attendance_month_id', $attendanceMonthIds);
                        })
                        ->count();
                } else {
                    $employeesMissingAttendance = $totalActiveEmployees;
                }
            }

            // Row 3: Recent 5 Payrolls (Eager load company and branch)
            $recentPayrolls = Payroll::forCurrentCompany()
                ->with(['company', 'branch'])
                ->withCount('details')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();

            // Row 4: Recent 5 Attendance Batches (Eager load company and branch)
            $recentAttendances = AttendanceMonth::forCurrentCompany()
                ->with(['company', 'branch'])
                ->withCount('details')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();

            // Row 6: Notifications
            $notifications = [];

            if ($employeesMissingSalary > 0) {
                $notifications[] = [
                    'type' => 'danger',
                    'icon' => 'bi-exclamation-triangle-fill',
                    'message' => "{$employeesMissingSalary} Employee(s) have no Salary Configuration",
                ];
            }

            $currentMonthName = Carbon::createFromDate($currentYear, $currentMonth, 1)->format('F Y');

            if ($isAllCompanies) {
                if ($attendancePendingCompanies > 0) {
                    $notifications[] = [
                        'type' => 'warning',
                        'icon' => 'bi-calendar-x-fill',
                        'message' => "Attendance pending for {$attendancePendingCompanies} of {$totalCompanies} companies for {$currentMonthName}",
                    ];
                }
                if ($payrollPendingCompanies > 0) {
                    $notifications[] = [
                        'type' => 'warning',
                        'icon' => 'bi-clock-history',
                        'message' => "Payroll pending for {$payrollPendingCompanies} of {$totalCompanies} companies for {$currentMonthName}",
                    ];
                }
            } else {
                if ($attendanceCompletedCompanies === 0) {
                    $notifications[] = [
                        'type' => 'warning',
                        'icon' => 'bi-calendar-x-fill',
                        'message' => "Attendance not generated for {$currentMonthName}",
                    ];
                }
                if ($payrollCompletedCompanies === 0) {
                    $notifications[] = [
                        'type' => 'warning',
                        'icon' => 'bi-clock-history',
                        'message' => "Payroll pending for {$currentMonthName}",
                    ];
                }
            }

            if (empty($notifications)) {
                $notifications[] = [
                    'type' => 'success',
                    'icon' => 'bi-check-circle-fill',
                    'message' => "All processing up to date for {$currentMonthName}",
                ];
            }

            return [
                'isAllCompanies' => $isAllCompanies,
                'totalCompanies' => $totalCompanies,
                'totalActiveEmployees' => $totalActiveEmployees,
                'totalDepartments' => $totalDepartments,
                'totalBranches' => $totalBranches,
                'attendanceCompletedCompanies' => $attendanceCompletedCompanies,
                'attendancePendingCompanies' => $attendancePendingCompanies,
                'attendanceCardStat' => $attendanceCardStat,
                'attendanceStatusText' => $attendanceStatusText,
                'attendanceBadgeText' => $attendanceBadgeText,
                'attendanceBadgeClass' => $attendanceBadgeClass,
                'payrollCompletedCompanies' => $payrollCompletedCompanies,
                'payrollPendingCompanies' => $payrollPendingCompanies,
                'payrollCardStat' => $payrollCardStat,
                'payrollStatusText' => $payrollStatusText,
                'payrollBadgeText' => $payrollBadgeText,
                'payrollBadgeClass' => $payrollBadgeClass,
                'monthlySalary' => $monthlySalary,
                'departmentDistribution' => $departmentDistribution,
                'employeesMissingSalary' => $employeesMissingSalary,
                'employeesMissingAttendance' => $employeesMissingAttendance,
                'recentPayrolls' => $recentPayrolls,
                'recentAttendances' => $recentAttendances,
                'notifications' => $notifications,
            ];
        });

        $currentCompany = CompanyScope::currentCompany();
        $user = Auth::user();

        return view('Admin.Dashboard.index', compact('stats', 'currentCompany', 'user', 'now'));
    }
}
