<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create([
            'firstname' => 'Ifeanyi',
            'lastname' => 'Okoro',
            'email' => 'okoro2020v@gmail.com',
            'password' => Hash::make('Qwertyuiop@1'),
            'email_verified_at' => now(),
            'phone_number' => '09092117814',
            'verification_code' => null,
            'role' => 'admin',
        ]);
    }
}
