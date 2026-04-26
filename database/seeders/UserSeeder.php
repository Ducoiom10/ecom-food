<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Super Admin',
            'phone'    => '0900000001',
            'email'    => 'admin@baanh.vn',
            'password' => Hash::make('password'),
            'role'     => 'super_admin',
        ]);

        User::create([
            'name'     => 'Minh Tuấn',
            'phone'    => '0901234567',
            'email'    => 'minhtuan@email.com',
            'password' => Hash::make('password'),
            'role'     => 'customer',
        ]);
    }
}
