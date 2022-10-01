<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $roles = Role::all();
        $librarian = $roles->where('name', 'librarian')->first();
        $student = $roles->where('name', 'student')->first();

        User::create([
            "first_name" => "Librarian",
            "last_name" => "Librarian",
            "email" => "librarian@librarian.test",
            'email_verified_at' => now(),
            'password' => Hash::make('secret1234')
        ])->assignRole($librarian);

        User::create([
            "first_name" => "Student",
            "last_name" => "Student",
            "email" => "student@student.test",
            'email_verified_at' => now(),
            'password' => Hash::make('secret1234')
        ])->assignRole($student);

        $users = User::factory()->count(10)->create();
        
        foreach ($users as $user) {
            $user->assignRole($roles->random());
        }
    }
}
