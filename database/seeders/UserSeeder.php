<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'mobile' => '9876543210',
                'password' => Hash::make('admin@123'),
                'role' => 'admin',
                'status' => 'active',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john.doe@spinhr.com',
                'mobile' => '9876543201',
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'status' => 'active',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@spinhr.com',
                'mobile' => '9876543202',
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'status' => 'active',
            ],
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul.sharma@spinhr.com',
                'mobile' => '9876543203',
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'status' => 'active',
            ],
            [
                'name' => 'Priya Patel',
                'email' => 'priya.patel@spinhr.com',
                'mobile' => '9876543204',
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'status' => 'active',
            ],
            [
                'name' => 'Amit Verma',
                'email' => 'amit.verma@spinhr.com',
                'mobile' => '9876543205',
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
