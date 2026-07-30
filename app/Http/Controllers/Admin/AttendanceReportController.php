<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceMonth;
use App\Models\AttendanceMonthDetail;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    /**
     * Display attendance report filter page.
     */
    public function index()
    {
        $companies = Company::where('status', 'active')->orderBy('name', 'asc')->get();
        $currentMonth = (int) date('m');
        $currentYear = (int) date('Y');

        return view('Admin.AttendanceReport.index', compact('companies', 'currentMonth', 'currentYear'));
    }

    /**
     * Get branches for a specific company (AJAX).
     */
    public function getBranches($companyId)
    {
        $branches = Branch::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json($branches);
    }

    /**
     * Get active employees for a company / branch (AJAX).
     */
    public function getEmployees(Request $request)
    {
        $query = Employee::where('status', 'active');

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $employees = $query->orderBy('employee_code', 'asc')->get(['id', 'employee_code', 'name']);

        return response()->json($employees);
    }

    /**
     * Build dynamic attendance query based on applied filters.
     */
    protected function getFilteredQuery(Request $request)
    {
        return AttendanceMonthDetail::query()
            ->with([
                'attendanceMonth.company',
                'attendanceMonth.branch',
                'employee.department',
                'employee.company',
                'employee.branch'
            ])
            ->whereHas('attendanceMonth', function ($q) use ($request) {
                $q->where('company_id', $request->company_id)
                  ->where('month', (int) $request->month)
                  ->where('year', (int) $request->year);

                if ($request->filled('branch_id')) {
                    $q->where('branch_id', $request->branch_id);
                }
            })
            ->when($request->filled('employee_id'), function ($q) use ($request) {
                $q->where('employee_id', $request->employee_id);
            });
    }

    /**
     * Generate monthly attendance report (AJAX).
     */
    public function report(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2000,2099',
            'branch_id' => 'nullable|exists:branches,id',
            'employee_id' => 'nullable|exists:employees,id',
        ], [
            'company_id.required' => 'Company is required.',
            'month.required' => 'Month is required.',
            'year.required' => 'Year is required.',
        ]);

        $records = $this->getFilteredQuery($request)->get();

        $company = Company::find($request->company_id);
        $branch = $request->filled('branch_id') ? Branch::find($request->branch_id) : null;
        $employee = $request->filled('employee_id') ? Employee::find($request->employee_id) : null;
        $month = (int) $request->month;
        $year = (int) $request->year;
        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        $html = view('Admin.AttendanceReport.report', compact('records', 'company', 'branch', 'employee', 'month', 'year', 'monthName'))->render();

        return response()->json([
            'status' => true,
            'html' => $html,
            'count' => $records->count(),
        ]);
    }

    /**
     * Export attendance report to Excel (CSV).
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2000,2099',
            'branch_id' => 'nullable|exists:branches,id',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $records = $this->getFilteredQuery($request)->get();

        $monthName = Carbon::createFromDate((int)$request->year, (int)$request->month, 1)->format('F');
        $fileName = 'Attendance_Report_' . $monthName . '_' . $request->year . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'Employee Code',
            'Employee Name',
            'Company',
            'Branch',
            'Department',
            'Month',
            'Year',
            'No. of Days in Month',
            'Leave Taken',
            'Net Present',
            'Leave Not Deducted',
            'No. of Days Payable'
        ];

        $callback = function () use ($records, $columns, $monthName, $request) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for proper Excel encoding
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);

            foreach ($records as $record) {
                $empCode = $record->employee->employee_code ?? '-';
                $empName = $record->employee->name ?? '-';
                $companyName = $record->attendanceMonth->company->name ?? ($record->employee->company->name ?? '-');
                $branchName = $record->attendanceMonth->branch->name ?? ($record->employee->branch->name ?? '-');
                $deptName = $record->employee->department->name ?? '-';
                $month = $monthName;
                $year = $request->year;

                $totalDays = (int) $record->total_days;
                $leaveTaken = (int) $record->leave_taken;
                $netPresent = (int) $record->net_present;
                $leaveNotDeducted = (int) $record->leave_not_deducted;
                $payableDays = (int) $record->payable_days;

                fputcsv($file, [
                    $empCode,
                    $empName,
                    $companyName,
                    $branchName,
                    $deptName,
                    $month,
                    $year,
                    $totalDays,
                    $leaveTaken,
                    $netPresent,
                    $leaveNotDeducted,
                    $payableDays
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export attendance report to PDF (Printable view).
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2000,2099',
            'branch_id' => 'nullable|exists:branches,id',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $records = $this->getFilteredQuery($request)->get();

        $company = Company::find($request->company_id);
        $branch = $request->filled('branch_id') ? Branch::find($request->branch_id) : null;
        $employee = $request->filled('employee_id') ? Employee::find($request->employee_id) : null;
        $month = (int) $request->month;
        $year = (int) $request->year;
        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

        return view('Admin.AttendanceReport.pdf', compact('records', 'company', 'branch', 'employee', 'month', 'year', 'monthName'));
    }
}
