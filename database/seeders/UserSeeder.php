<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            "first_name" => "Librarian",
            "last_name" => "Librarian",
            "email" => "librarian@librarian.test",
            'email_verified_at' => now(),
            'password' => Hash::make('secret1234')
        ]);
        User::create([
            "first_name" => "Student",
            "last_name" => "Student",
            "email" => "student@student.test",
            'email_verified_at' => now(),
            'password' => Hash::make('secret1234')
        ]);
    }
}