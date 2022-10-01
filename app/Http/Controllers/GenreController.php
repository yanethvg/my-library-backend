<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\GenreResource;
use App\Models\Genre;

class GenreController extends Controller
{
    public function index () {
        return GenreResource::collection(Genre::all());
    }
}
