<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Genre;

class BookFactory extends Factory
{
    public function definition()
    {
        $genresIds = Genre::all()->pluck('id');
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name,
            'genre_id' => $this->faker->randomElement($genresIds),
            'year_published' => $this->faker->year,
            'stock' => $this->faker->numberBetween(5, 100)
        ];
    }
}
