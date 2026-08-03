<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\AttendanceMonth;

class AttendanceLockService
{
    /**
     * Check if attendance is locked for a company, year, and month.
     * Locked ONLY when ALL active branches of the selected company have an AttendanceMonth record
     * with status = 'Completed' for the selected month and year.
     *
     * @param int|null $companyId
     * @param int $year
     * @param int $month
     * @return bool
     */
    public static function isLocked($companyId, int $year, int $month): bool
    {
        if (!$companyId) {
            return false;
        }

        $year = (int) $year;
        $month = (int) $month;

        // Get count of active branches belonging to the company
        $totalActiveBranches = Branch::where('company_id', $companyId)
            ->where('status', 'active')
            ->count();

        if ($totalActiveBranches === 0) {
            return false;
        }

        // Get count of unique active branches that have COMPLETED attendance for this year/month
        $completedBranchesCount = AttendanceMonth::where('company_id', $companyId)
            ->where('year', $year)
            ->where('month', $month)
            ->where('status', 'Completed')
            ->whereHas('branch', function ($q) {
                $q->where('status', 'active');
            })
            ->distinct('branch_id')
            ->count('branch_id');

        return $completedBranchesCount >= $totalActiveBranches;
    }
}
