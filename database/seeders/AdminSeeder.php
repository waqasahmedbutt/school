<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@school.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('change-me-123'),
                'role' => 'admin',
                'cnic' => null,
                'is_active' => true,
            ]
        );
    }
}
