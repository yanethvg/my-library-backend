<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\StudentResource;
use App\Http\Resources\BookResource;
use App\Models\User;
use App\Models\Book;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = User::role('student')->fullName($request->search)->paginate(10);
        return StudentResource::collection($students);
    }

    public function books(Request $request)
    {
        $user = auth()->user();
        $books = Book::with('genre')
            ->genre($request->genre)
            ->title($request->title)
            ->author($request->author)
        ->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->paginate(10);
        return BookResource::collection($books);
    }

    public function books_student(Request $request,$id)
    {
        $books = Book::with('genre')
            ->genre($request->genre)
            ->title($request->title)
            ->author($request->author)
        ->whereHas('users', function ($query) use ($id) {
            $query->where('user_id', $id);
        })->paginate(10);
        return BookResource::collection($books);
    }

}
