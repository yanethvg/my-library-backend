<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'genre_id',
        'year_published',
        'stock'
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function scopeGenre($query, $genre)
    {
        if ($genre)
            return $query->whereHas('genre', function ($query) use ($genre) {
                $query->where('name', 'ilike', "%$genre%");
            });
    }
    public function scopeTitle($query, $title)
    {
        if ($title)
            return $query->where('title', 'ilike', "%$title%");
    }

    public function scopeAuthor($query, $author)
    {
        if ($author)
            return $query->where('author', 'ilike', "%$author%");
    }
}
