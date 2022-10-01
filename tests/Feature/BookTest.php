<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\GenreSeeder;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;

class BookTest extends TestCase
{
   // refresh database
   use RefreshDatabase;

   protected $token;
   protected $user;

   public function setUp(): void
   {
       parent::setUp();
       $this->authenticate();
   }

   protected function authenticate(){
       $this->user = User::create([
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

       
       $this->token = $response->json('access_token');
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

    public function test_books_can_not_get_books_without_authorization()
    {
        $response = $this->json('GET', '/api/books', [], [
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
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
    public function test_book_can_not_be_created_wrong_genre()
    {
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
    
         $response->assertUnprocessable();
         $response->assertJsonStructure([
              'message',
              'errors' => [
                'genre_id'
              ]
         ]);
    }
    public function test_book_can_not_be_created_without_title()
    {
         $body = [
              'author' => 'J.R.R. Tolkien',
              'genre_id' => 1,
              'year_published' => "1954",
              'stock' => 10
         ];
    
         $response = $this->json('POST', '/api/books', $body, [
              'Accept' => 'application/json',
              'Authorization' => 'Bearer ' . $this->token
         ]);
    
         $response->assertUnprocessable();
         $response->assertJsonStructure([
              'message',
              'errors' => [
                'title'
              ]
         ]);
    }
    public function test_book_can_not_be_created_without_author()
    {
         $body = [
            'title' => 'The Lord of the Rings',
              'genre_id' => 1,
              'year_published' => "1954",
              'stock' => 10
         ];
    
         $response = $this->json('POST', '/api/books', $body, [
              'Accept' => 'application/json',
              'Authorization' => 'Bearer ' . $this->token
         ]);
    
         $response->assertUnprocessable();
         $response->assertJsonStructure([
              'message',
              'errors' => [
                'author'
              ]
         ]);
    }
    public function test_book_can_not_be_created_without_year_published()
    {
         $body = [
            'title' => 'The Lord of the Rings',
              'author' => 'J.R.R. Tolkien',
              'genre_id' => 1,
              'stock' => 10
         ];
    
         $response = $this->json('POST', '/api/books', $body, [
              'Accept' => 'application/json',
              'Authorization' => 'Bearer ' . $this->token
         ]);
    
         $response->assertUnprocessable();
         $response->assertJsonStructure([
              'message',
              'errors' => [
                'year_published'
              ]
         ]);
    }
    public function test_book_can_not_be_created_without_stock()
    {
         $body = [
            'title' => 'The Lord of the Rings',
              'author' => 'J.R.R. Tolkien',
              'genre_id' => 1,
              'year_published' => "1954",
         ];
    
         $response = $this->json('POST', '/api/books', $body, [
              'Accept' => 'application/json',
              'Authorization' => 'Bearer ' . $this->token
         ]);
    
         $response->assertUnprocessable();
         $response->assertJsonStructure([
              'message',
              'errors' => [
                'stock'
              ]
         ]);
    }
    
    public function test_book_can_not_be_created_wrong_without_authorization()
    {
         $body = [
              'title' => 'The Lord of the Rings',
              'author' => 'J.R.R. Tolkien',
              'genre_id' => 1,
              'year_published' => "1954",
              'stock' => 10
         ];
    
         $response = $this->json('POST', '/api/books', $body, [
              'Accept' => 'application/json'
         ]);
    
         $response->assertStatus(401);
         $response->assertJsonStructure([
              'message'
         ]);
    }

    public function test_get_book()
    {
        $this->seed(GenreSeeder::class);
        $book = Book::factory()->create();
        $response = $this->json('GET', '/api/books/' . $book->id, [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'author',
                'genre' => [
                    'id',
                    'name',
                ],
                'stock',
                'year_published',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_wrong_get_book_with_bad_id()
    {
        $response = $this->json('GET', '/api/books/200', [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_wrong_get_book_without_authorization()
    {
        $book = Book::factory()->create();
        $response = $this->json('GET', '/api/books/' . $book->id, [
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_book_can_be_borrow()
    {
        $this->seed(GenreSeeder::class);
        $book = Book::factory()->create();
        
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ])->json('POST', '/api/books/'.$book->id.'/borrow');
        

        $response->assertStatus(200);
       
        $response->assertJsonStructure([
            'message',
            'book' => [
                'id',
                'title',
                'author',
                'genre' => [
                    'id',
                    'name',
                ],
                'stock',
                'year_published',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_book_can_not_be_borrow_without_authorization()
    {
        $book = Book::factory()->create();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST', '/api/books/'.$book->id.'/borrow');

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_book_can_not_be_borrow_without_stock()
    {
        $this->seed(GenreSeeder::class);
        $book = Book::factory()->create([
            'stock' => 0
        ]);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ])->json('POST', '/api/books/'.$book->id.'/borrow');

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_book_can_not_be_borrow_user_borrowed()
    {
        $this->seed(GenreSeeder::class);
        $book = Book::factory()->create();
        $book->users()->attach($this->user->id);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ])->json('POST', '/api/books/'.$book->id.'/borrow');

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_book_can_not_be_return()
    {
        $this->seed(GenreSeeder::class);
        $book = Book::factory()->create();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ])->json('POST', '/api/books/'.$book->id.'/return');

        // dd($response->getContent());

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'message'
        ]);
    }

}
