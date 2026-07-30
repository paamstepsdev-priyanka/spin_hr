<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Information Technology', 'status' => 'active'],
            ['name' => 'Human Resources', 'status' => 'active'],
            ['name' => 'Finance & Accounting', 'status' => 'active'],
            ['name' => 'Sales & Marketing', 'status' => 'active'],
            ['name' => 'Operations & Logistics', 'status' => 'active'],
        ];

        foreach ($departments as $deptData) {
            Department::updateOrCreate(
                ['name' => $deptData['name']],
                $deptData
            );
        }
    }
}
