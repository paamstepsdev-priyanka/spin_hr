<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeBulkSeeder extends Seeder
{
    /**
     * Seed 5 employees for each branch of every company.
     */
    public function run(): void
    {
        $companies = Company::with('branches')->get();
        $departments = Department::all();

        if ($departments->isEmpty()) {
            return;
        }

        $names = [
            'Aarav Sharma', 'Vivaan Patel', 'Aditya Verma', 'Vihaan Gupta', 'Arjun Singh',
            'Sanya Rao', 'Ananya Kumar', 'Isha Mehta', 'Diya Joshi', 'Riya Nair',
            'Rohan Deshmukh', 'Karan Malhotra', 'Vikram Choudhury', 'Siddharth Roy', 'Manish Kapoor',
            'Neha Agarwal', 'Pooja Bhatt', 'Kavya Saxena', 'Sneha Reddy', 'Priya Kulkarni',
            'Rahul Chatterji', 'Amit Das', 'Deepak Mishra', 'Sanjay Pandey', 'Alok Banerjee'
        ];

        $designations = [
            'Software Engineer', 'HR Executive', 'Accountant', 'Sales Executive', 'Marketing Specialist',
            'Operations Associate', 'Team Lead', 'Business Analyst', 'Support Specialist', 'Quality Analyst'
        ];

        $empCount = Employee::count();

        foreach ($companies as $company) {
            foreach ($company->branches as $branch) {
                // Count existing active employees for this specific company & branch
                $existingCount = Employee::where('company_id', $company->id)
                    ->where('branch_id', $branch->id)
                    ->where('status', 'active')
                    ->count();

                $needed = max(0, 5 - $existingCount);

                for ($i = 1; $i <= $needed; $i++) {
                    $empCount++;
                    $empCode = 'EMP' . str_pad($empCount, 3, '0', STR_PAD_LEFT);
                    $rawName = $names[($empCount - 1) % count($names)];
                    $name = $rawName;
                    $cleanName = strtolower(preg_replace('/[^a-zA-Z]/', '.', $rawName));
                    $email = $cleanName . '.' . $empCount . '@spinhr.com';
                    $mobile = '98' . str_pad($empCount, 8, '0', STR_PAD_LEFT);
                    $dept = $departments[($empCount - 1) % $departments->count()];
                    $designation = $designations[($empCount - 1) % count($designations)];

                    // Create user account for login
                    $user = User::firstOrCreate(
                        ['email' => $email],
                        [
                            'name' => $name,
                            'mobile' => $mobile,
                            'password' => Hash::make('password123'),
                            'role' => 'employee',
                            'status' => 'active',
                        ]
                    );

                    // Create employee record
                    Employee::updateOrCreate(
                        ['employee_code' => $empCode],
                        [
                            'user_id' => $user->id,
                            'company_id' => $company->id,
                            'branch_id' => $branch->id,
                            'department_id' => $dept->id,
                            'name' => $name,
                            'father_name' => 'Father of ' . $name,
                            'email' => $email,
                            'mobile' => $mobile,
                            'designation' => $designation,
                            'employment_type' => ($empCount % 2 == 0) ? 'Permanent' : 'Consultant',
                            'reporting_to' => 'Manager ' . $dept->name,
                            'joining_date' => '2024-01-' . str_pad(($i % 28) + 1, 2, '0', STR_PAD_LEFT),
                            'dob' => '1995-05-' . str_pad(($i % 28) + 1, 2, '0', STR_PAD_LEFT),
                            'gender' => ($i % 2 == 0) ? 'Female' : 'Male',
                            'marital_status' => ($i % 2 == 0) ? 'Married' : 'Single',
                            'accommodation_type' => 'Rented',
                            'city' => $branch->city ?? 'City',
                            'state' => $branch->state ?? 'State',
                            'zip_code' => $branch->zip_code ?? '400001',
                            'address_line1' => 'Street ' . $i . ', ' . ($branch->name ?? 'Main Branch'),
                            'account_holder_name' => $name,
                            'account_no' => '91' . str_pad($empCount, 10, '0', STR_PAD_LEFT),
                            'ifsc_code' => 'SBIN000' . str_pad($company->id, 4, '0', STR_PAD_LEFT),
                            'bank_name' => 'State Bank of India',
                            'bank_branch_name' => ($branch->name ?? 'Main') . ' Branch',
                            'status' => 'active',
                        ]
                    );
                }
            }
        }
    }
}
