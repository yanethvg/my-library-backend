<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genres = [ "Aventura", "Comedia", "Paranormal", "Drama", "Policiaca", "Romance", "Terror", "Cuentos", "Fantasia", "Ciencia Ficcion", "Historica", "Biografica", "Autoayuda", "Religiosa", "Infantil", "Juvenil", "Adultos", "Otros" ];	

        foreach ($genres as $genre) {
            Genre::create([
                'name' => $genre
            ]);
        }
    }
}
