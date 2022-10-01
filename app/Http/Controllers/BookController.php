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
}
