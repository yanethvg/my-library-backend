<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'books.index']);
        Permission::create(['name' => 'books.show']);
        Permission::create(['name' => 'books.store']);
        Permission::create(['name' => 'books.borrow']);
        Permission::create(['name' => 'books.return']);
        Permission::create(['name' => 'books.students']);

        Permission::create(['name' => 'students.index']);
        Permission::create(['name' => 'students.books']);
        // complementary resources
        Permission::create(['name' => 'genres.index']);
        Permission::create(['name' => 'roles.index']);
        Permission::create(['name' => 'users.store']);
        Permission::create(['name' => 'users.index']);
    }
}
