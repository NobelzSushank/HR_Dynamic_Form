<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@company.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
            ]
        );
        
        $admin->assignRole(['Admin']);
        
        // HR
        $hr = User::firstOrCreate(
            ['email' => 'hr@company.com'],
            [
                'name' => 'HR Manager',
                'password' => Hash::make('password'),
            ]
        );
        
        $hr->assignRole(['HR']);
        
        // Employee
        $employee = User::firstOrCreate(
            ['email' => 'employee@company.com'],
            [
                'name' => 'John Employee',
                'password' => Hash::make('password'),
            ]
        );
        $employee->assignRole(['Employee']);
    }
}
