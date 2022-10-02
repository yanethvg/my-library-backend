<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use App\Http\Requests\CreateBookRequest;
use App\Models\Book;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with('genre')
            ->genre($request->genre)
            ->title($request->title)
            ->author($request->author)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
        return BookResource::collection($books);
    }

    public function show($id)
    {
        return new BookResource(Book::findOrFail($id));
    }

    public function store(CreateBookRequest $request)
    {
        $book = Book::create($request->all());
        return new BookResource($book);
    }
    public function borrow($id)
    {
        $book = Book::findOrFail($id);
        if ($book->stock == 0) {
            return response()->json([
                'message' => 'Book is out of stock'
            ], 400);
        }
        if ($book->users()->where('user_id', auth()->user()->id)->exists()) {
            return response()->json([
                'message' => 'You already borrowed this book'
            ], 400);
        }
        $book->stock = $book->stock - 1;
        $book->save();
        $book->users()->attach(auth()->user()->id);
        return [
            'message' => 'Book borrowed successfully',
            'book' => new BookResource($book)
        ];
    }
    public function return($id, $student_id)
    {
        $book = Book::findOrFail($id);
        if (!$book->users()->where('user_id', $student_id)->exists()) {
            return response()->json([
                'message' => 'You have not borrowed this book'
            ], 400);
        }
        $book->stock = $book->stock + 1;
        $book->save();
        $book->users()->detach($student_id);
        return [
            'message' => 'Book returned successfully',
            'book' => new BookResource($book)
        ];
    }
}
