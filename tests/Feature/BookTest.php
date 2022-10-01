<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\GenreSeeder;
use Tests\TestCase;
use App\Models\User;

class BookTest extends TestCase
{
   // refresh database
   use RefreshDatabase;

   protected $token;

   public function setUp(): void
   {
       parent::setUp();
       $this->token = $this->authenticate();
   }

   protected function authenticate(){
       $user = User::create([
           'first_name'=> 'Joe',
           'last_name'=> 'Doe',
           'password' => 'testtest',
           'email' => 'joeDoe@test.com',
           'password' => Hash::make('testtest')
       ]);

       $body =[
           'email' => 'joeDoe@test.com',
           'password' => 'testtest'
       ];

       $response =$this->json('POST','/api/login',$body,['Accept' => 'application/json']);

       return $response->json('access_token');
   }

   public function test_all_books()
   {
         $response = $this->json('GET', '/api/books', [], [
              'Authorization' => 'Bearer ' . $this->token,
              'Accept' => 'application/json'
         ]);
    
         $response->assertStatus(200);
            $response->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'genre' => [
                            'id',
                            'name'
                        ],
                        'stock',
                        'year_published',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total'
                ]
            ]);
   }

    public function test_book_can_be_created()
    {
        $this->seed(GenreSeeder::class);
         $body = [
              'title' => 'The Lord of the Rings',
              'author' => 'J.R.R. Tolkien',
              'genre_id' => 1,
              'year_published' => "1954",
              'stock' => 10
         ];
    
         $response = $this->json('POST', '/api/books', $body, [
              'Accept' => 'application/json',
              'Authorization' => 'Bearer ' . $this->token
         ]);
    
         $response->assertStatus(201);
         $response->assertJsonStructure([
              'data' => [
                'id',
                'title',
                'author',
                'genre',
                'stock',
                'year_published',
                'created_at',
                'updated_at'
              ]
         ]);
    }

}
