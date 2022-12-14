<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'librarian']);
        $role->givePermissionTo([
            'books.index',
            'books.show',
            'books.store',
            'genres.index',
            'roles.index',
            'books.return',
            'users.store',
            'books.students',
            'students.index',
            'users.index'
        ]);
        $role = Role::create(['name' => 'student']);
        $role->givePermissionTo([
            'books.index',
            'books.show',
            'books.borrow',
            'students.books',
            'genres.index',
        ]);
    }
}
