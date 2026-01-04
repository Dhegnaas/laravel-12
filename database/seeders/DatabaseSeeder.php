<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        // Create default role if it doesn't exist
        $role = Role::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Admin',
                'pages' => [],
                'check_all' => true,
                'description' => 'Administrator role with full access',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'fullname' => 'Admin User',
                'email' => 'admin@gmail.com',
                'phone' => '0688168726',
                'password' => 'password', // The User model's mutator will hash this automatically
                'role_id' => $role->id,
                'is_blocked' => false,
                'is_superadmin' => true,
                'status' => 'submitted',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
