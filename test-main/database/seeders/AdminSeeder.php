<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //$profilePath = file_get_contents('C:\Users\TECH SHOP\Desktop\BackEnd\testGit\storage\app\public\profile_picture\Admin.jpg');
        //$nationPath = file_get_contents('C:\Users\TECH SHOP\Desktop\BackEnd\testGit\storage\app\public\nation_picture\Admin.jpg');

        $profilePath = 'profile_picture/admin.jpg';  // Relative path in storage
        $nationPath = 'nation_picture/admin.jpg';
        
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'System',
            'phone_number' => '0999999999',
            'password' => Hash::make('3232066'),
            'user_state' => 'admin',
            'status' => 'approved',
            'date_of_birth' => '1990-01-01',
            'profile_picture' => $profilePath,
            'nation_picture' => $nationPath,
        ]);
    }
}
