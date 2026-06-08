<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if ($admin) {
            $admin->update([
                'pass_user' => Hash::make('password') // Setting password to 'password'
            ]);
        } else {
            User::create([
                'nama_user' => 'admin',
                'pass_user' => Hash::make('password'),
                'role' => 'admin'
            ]);
        }
    }
}
