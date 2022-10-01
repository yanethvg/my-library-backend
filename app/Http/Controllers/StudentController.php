<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\StudentResource;
use App\Http\Resources\BookResource;
use App\Models\User;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = User::role('student')->fullName($request->search)->paginate(10);
        return StudentResource::collection($students);
    }

    public function books()
    {
        $user = auth()->user();
        $books = User::findOrFail($user->id)->books;
        return BookResource::collection($books);
    }
}
