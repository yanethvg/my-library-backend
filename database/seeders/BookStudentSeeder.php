<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;

class BookStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $students = User::role('student')->get();
        $books = Book::all();

        foreach ($students as $student) {
            $student->books()->attach(
                $books->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}
