<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::create([
            'firstname' => 'Admin',
            'lastname' => 'Admin',
            'email' => 'admin@allmight.com',
            'password' => Hash::make('password')
        ]);

        $admin->assignRole('admin');
    }
}
