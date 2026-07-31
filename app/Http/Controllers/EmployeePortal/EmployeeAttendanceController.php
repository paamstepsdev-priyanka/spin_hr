<?php

namespace App\Http\Controllers\EmployeePortal;

use App\Http\Controllers\Controller;
use App\Models\AttendanceMonthDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeAttendanceController extends Controller
{
    /**
     * Display employee attendance history table with filters.
     */
    public function index(Request $request)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            abort(403, 'Unauthorized access.');
        }

        $query = AttendanceMonthDetail::where('employee_id', $employee->id)
            ->with(['attendanceMonth'])
            ->whereHas('attendanceMonth', function ($q) use ($request) {
                if ($request->filled('month')) {
                    $q->where('month', (int) $request->month);
                }
                if ($request->filled('year')) {
                    $q->where('year', (int) $request->year);
                }
            })
            ->orderBy('id', 'desc');

        $attendances = $query->paginate(12)->withQueryString();

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return view('Employee.attendance', compact('attendances', 'months'));
    }
}
