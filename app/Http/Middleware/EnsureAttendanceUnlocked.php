<?php

namespace App\Http\Middleware;

use App\Models\AttendanceMonth;
use App\Services\AttendanceLockService;
use App\Services\CompanyScope;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAttendanceUnlocked
{
    /**
     * Handle an incoming request for Attendance Edit / Update / Destroy routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $attendance = $request->route('attendance');

        if ($attendance) {
            $attendanceMonth = ($attendance instanceof AttendanceMonth) 
                ? $attendance 
                : AttendanceMonth::find($attendance);

            if ($attendanceMonth) {
                $companyId = $attendanceMonth->company_id ?? CompanyScope::id();
                
                if (AttendanceLockService::isLocked($companyId, (int)$attendanceMonth->year, (int)$attendanceMonth->month)) {
                    $msg = 'Attendance has been locked because all company branches have completed attendance for this month.';
                    
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'status' => false,
                            'message' => $msg,
                        ], 403);
                    }

                    abort(403, $msg);
                }
            }
        }

        return $next($request);
    }
}
