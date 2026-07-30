<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Database\Seeder;

class EmployeeSalarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all()->keyBy('employee_code');

        $salaries = [
            [
                'employee_id' => $employees['EMP001']->id ?? 1,
                'basic_salary' => 40000.00,
                'variable_allowance' => 5000.00,
                'hra' => 16000.00,
                'conveyance_allowance' => 3000.00,
                'medical_allowance' => 2000.00,
                'special_allowance' => 9000.00,
                'other_allowance' => 0.00,
                'employee_pf' => 4800.00,
                'esi' => 0.00,
                'professional_tax' => 200.00,
                'tds' => 2500.00,
                'other_deduction' => 0.00,
                'gross_salary' => 75000.00,
                'total_deduction' => 7500.00,
                'net_salary' => 67500.00,
                'effective_from' => '2024-01-15',
                'effective_to' => null,
                'status' => 'active',
            ],
            [
                'employee_id' => $employees['EMP002']->id ?? 2,
                'basic_salary' => 50000.00,
                'variable_allowance' => 7000.00,
                'hra' => 20000.00,
                'conveyance_allowance' => 4000.00,
                'medical_allowance' => 2500.00,
                'special_allowance' => 11500.00,
                'other_allowance' => 0.00,
                'employee_pf' => 6000.00,
                'esi' => 0.00,
                'professional_tax' => 200.00,
                'tds' => 3500.00,
                'other_deduction' => 0.00,
                'gross_salary' => 95000.00,
                'total_deduction' => 9700.00,
                'net_salary' => 85300.00,
                'effective_from' => '2023-06-01',
                'effective_to' => null,
                'status' => 'active',
            ],
            [
                'employee_id' => $employees['EMP003']->id ?? 3,
                'basic_salary' => 35000.00,
                'variable_allowance' => 4000.00,
                'hra' => 14000.00,
                'conveyance_allowance' => 2500.00,
                'medical_allowance' => 1500.00,
                'special_allowance' => 8000.00,
                'other_allowance' => 0.00,
                'employee_pf' => 4200.00,
                'esi' => 0.00,
                'professional_tax' => 200.00,
                'tds' => 1800.00,
                'other_deduction' => 0.00,
                'gross_salary' => 65000.00,
                'total_deduction' => 6200.00,
                'net_salary' => 58800.00,
                'effective_from' => '2024-03-10',
                'effective_to' => null,
                'status' => 'active',
            ],
            [
                'employee_id' => $employees['EMP004']->id ?? 4,
                'basic_salary' => 30000.00,
                'variable_allowance' => 3500.00,
                'hra' => 12000.00,
                'conveyance_allowance' => 2000.00,
                'medical_allowance' => 1500.00,
                'special_allowance' => 6000.00,
                'other_allowance' => 0.00,
                'employee_pf' => 3600.00,
                'esi' => 0.00,
                'professional_tax' => 200.00,
                'tds' => 1200.00,
                'other_deduction' => 0.00,
                'gross_salary' => 55000.00,
                'total_deduction' => 5000.00,
                'net_salary' => 50000.00,
                'effective_from' => '2024-02-01',
                'effective_to' => null,
                'status' => 'active',
            ],
            [
                'employee_id' => $employees['EMP005']->id ?? 5,
                'basic_salary' => 45000.00,
                'variable_allowance' => 6000.00,
                'hra' => 18000.00,
                'conveyance_allowance' => 3500.00,
                'medical_allowance' => 2000.00,
                'special_allowance' => 10500.00,
                'other_allowance' => 0.00,
                'employee_pf' => 5400.00,
                'esi' => 0.00,
                'professional_tax' => 200.00,
                'tds' => 3000.00,
                'other_deduction' => 0.00,
                'gross_salary' => 85000.00,
                'total_deduction' => 8600.00,
                'net_salary' => 76400.00,
                'effective_from' => '2023-09-15',
                'effective_to' => null,
                'status' => 'active',
            ],
        ];

        foreach ($salaries as $salaryData) {
            EmployeeSalary::updateOrCreate(
                ['employee_id' => $salaryData['employee_id'], 'effective_from' => $salaryData['effective_from']],
                $salaryData
            );
        }
    }
}
