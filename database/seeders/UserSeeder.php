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
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'Admin',
                'last_name'  => 'Admin',
                'email'      => 'admin@gmail.com',
                'password'   => Hash::make('admin@123'),
                'role'       => 'admin',
                'status'     => 'active',
            ]
        );
    }
}
