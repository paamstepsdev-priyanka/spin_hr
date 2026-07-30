<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all()->keyBy('name');
        $branches = Branch::all()->keyBy('name');
        $departments = Department::all()->keyBy('name');
        $users = User::all()->keyBy('email');

        $employees = [
            [
                'employee_code' => 'EMP001',
                'user_id' => $users['john.doe@spinhr.com']->id ?? null,
                'company_id' => $companies['TechCorp Solutions']->id ?? 1,
                'branch_id' => $branches['Mumbai Head Office']->id ?? 1,
                'department_id' => $departments['Information Technology']->id ?? 1,
                'name' => 'John Doe',
                'father_name' => 'Robert Doe',
                'email' => 'john.doe@spinhr.com',
                'mobile' => '9876543201',
                'designation' => 'Senior Software Engineer',
                'employment_type' => 'Permanent',
                'reporting_to' => 'Technical Lead',
                'joining_date' => '2024-01-15',
                'dob' => '1992-05-20',
                'gender' => 'Male',
                'marital_status' => 'Single',
                'accommodation_type' => 'Rented',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'zip_code' => '400051',
                'address_line1' => 'Flat 402, Sunshine Apartments, BKC',
                'account_holder_name' => 'John Doe',
                'account_no' => '918273645012',
                'ifsc_code' => 'SBIN0001234',
                'bank_name' => 'State Bank of India',
                'bank_branch_name' => 'BKC Branch',
                'status' => 'active',
            ],
            [
                'employee_code' => 'EMP002',
                'user_id' => $users['jane.smith@spinhr.com']->id ?? null,
                'company_id' => $companies['Acme Global Enterprises']->id ?? 2,
                'branch_id' => $branches['Bengaluru Tech Hub']->id ?? 2,
                'department_id' => $departments['Human Resources']->id ?? 2,
                'name' => 'Jane Smith',
                'father_name' => 'William Smith',
                'email' => 'jane.smith@spinhr.com',
                'mobile' => '9876543202',
                'designation' => 'HR Manager',
                'employment_type' => 'Permanent',
                'reporting_to' => 'VP Human Resources',
                'joining_date' => '2023-06-01',
                'dob' => '1990-11-12',
                'gender' => 'Female',
                'marital_status' => 'Married',
                'accommodation_type' => 'Owned',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'zip_code' => '560001',
                'address_line1' => 'House No 12, Indiranagar',
                'account_holder_name' => 'Jane Smith',
                'account_no' => '918273645013',
                'ifsc_code' => 'HDFC0005678',
                'bank_name' => 'HDFC Bank',
                'bank_branch_name' => 'MG Road Branch',
                'status' => 'active',
            ],
            [
                'employee_code' => 'EMP003',
                'user_id' => $users['rahul.sharma@spinhr.com']->id ?? null,
                'company_id' => $companies['Apex HR Systems']->id ?? 3,
                'branch_id' => $branches['Delhi Regional Office']->id ?? 3,
                'department_id' => $departments['Finance & Accounting']->id ?? 3,
                'name' => 'Rahul Sharma',
                'father_name' => 'Suresh Sharma',
                'email' => 'rahul.sharma@spinhr.com',
                'mobile' => '9876543203',
                'designation' => 'Senior Accountant',
                'employment_type' => 'Permanent',
                'reporting_to' => 'Finance Director',
                'joining_date' => '2024-03-10',
                'dob' => '1988-08-15',
                'gender' => 'Male',
                'marital_status' => 'Married',
                'accommodation_type' => 'Rented',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'zip_code' => '110001',
                'address_line1' => 'Block C, Connaught Place',
                'account_holder_name' => 'Rahul Sharma',
                'account_no' => '918273645014',
                'ifsc_code' => 'ICIC0009101',
                'bank_name' => 'ICICI Bank',
                'bank_branch_name' => 'Connaught Place Branch',
                'status' => 'active',
            ],
            [
                'employee_code' => 'EMP004',
                'user_id' => $users['priya.patel@spinhr.com']->id ?? null,
                'company_id' => $companies['Quantum Innovations']->id ?? 4,
                'branch_id' => $branches['Hyderabad R&D Center']->id ?? 4,
                'department_id' => $departments['Sales & Marketing']->id ?? 4,
                'name' => 'Priya Patel',
                'father_name' => 'Ramesh Patel',
                'email' => 'priya.patel@spinhr.com',
                'mobile' => '9876543204',
                'designation' => 'Marketing Lead',
                'employment_type' => 'Consultant',
                'reporting_to' => 'Chief Marketing Officer',
                'joining_date' => '2024-02-01',
                'dob' => '1995-03-25',
                'gender' => 'Female',
                'marital_status' => 'Single',
                'accommodation_type' => 'Rented',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'zip_code' => '500081',
                'address_line1' => 'Plot 55, Gachibowli',
                'account_holder_name' => 'Priya Patel',
                'account_no' => '918273645015',
                'ifsc_code' => 'UTIB0001122',
                'bank_name' => 'Axis Bank',
                'bank_branch_name' => 'HITEC City Branch',
                'status' => 'active',
            ],
            [
                'employee_code' => 'EMP005',
                'user_id' => $users['amit.verma@spinhr.com']->id ?? null,
                'company_id' => $companies['Synergy Infotech']->id ?? 5,
                'branch_id' => $branches['Pune Development Center']->id ?? 5,
                'department_id' => $departments['Operations & Logistics']->id ?? 5,
                'name' => 'Amit Verma',
                'father_name' => 'Vijay Verma',
                'email' => 'amit.verma@spinhr.com',
                'mobile' => '9876543205',
                'designation' => 'Operations Manager',
                'employment_type' => 'Permanent',
                'reporting_to' => 'Head of Operations',
                'joining_date' => '2023-09-15',
                'dob' => '1991-07-04',
                'gender' => 'Male',
                'marital_status' => 'Married',
                'accommodation_type' => 'Owned',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'zip_code' => '411057',
                'address_line1' => 'Flat 101, Green Meadows, Hinjewadi',
                'account_holder_name' => 'Amit Verma',
                'account_no' => '918273645016',
                'ifsc_code' => 'KKBK0003344',
                'bank_name' => 'Kotak Mahindra Bank',
                'bank_branch_name' => 'Hinjewadi Branch',
                'status' => 'active',
            ],
        ];

        foreach ($employees as $employeeData) {
            Employee::updateOrCreate(
                ['employee_code' => $employeeData['employee_code']],
                $employeeData
            );
        }
    }
}
