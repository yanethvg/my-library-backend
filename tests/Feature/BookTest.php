<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\GenreSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;

class BookTest extends TestCase
{
   // refresh database
   use RefreshDatabase;

   protected $librarian;
   protected $student;

   public function setUp(): void
   {
       parent::setUp();
       $this->seed(PermissionSeeder::class);
       $this->seed(RoleSeeder::class);
       $this->createUser();
   }

   protected function createUser()
   {
        $this->librarian = User::create([
            'first_name'=> 'Joe',
            'last_name'=> 'Doe',
            'password' => 'testtest',
            'email' => 'joeDoe@test.com',
            'password' => Hash::make('testtest')
        ])->assignRole('librarian');

        $this->student = User::create([
            'first_name'=> 'Dan',
            'last_name'=> 'Doe',
            'password' => 'testtest',
            'email' => 'danDoe@test.com',
            'password' => Hash::make('testtest')
        ])->assignRole('student');
   }

   protected function authenticate($librarian){
       
       if($librarian){
        $body =[
            'email' => 'joeDoe@test.com',
            'password' => 'testtest'
        ];
       }else{
        $body =[
            'email' => 'danDoe@test.com',
            'password' => 'testtest'
        ];
       }

       $response =$this->json('POST','/api/login',$body,['Accept' => 'application/json']);

       
       return $response->json('access_token');
   }


   public function test_all_books_students()
   {
        $token = $this->authenticate(false);
         $response = $this->json('GET', '/api/books', [], [
              'Authorization' => 'Bearer ' . $token,
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
   public function test_all_books_librarians()
   {
        $token = $this->authenticate(true);
         $response = $this->json('GET', '/api/books', [], [
              'Authorization' => 'Bearer ' . $token,
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
        $token = $this->authenticate(true);
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
              'Authorization' => 'Bearer ' . $token
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
    public function test_book_can_not_be_created_without_permission_store()
    {
        $token = $this->authenticate(false);
         $body = [
              'title' => 'The Lord of the Rings',
              'author' => 'J.R.R. Tolkien',
              'genre_id' => 1,
              'year_published' => "1954",
              'stock' => 10
         ];
    
         $response = $this->json('POST', '/api/books', $body, [
              'Accept' => 'application/json',
              'Authorization' => 'Bearer ' . $token
         ]);
    
         $response->assertStatus(403);
         $response->assertJsonStructure([
              'message'
         ]);
    }
    public function test_book_can_not_be_created_wrong_genre()
    {
        $token = $this->authenticate(true);
         $body = [
              'title' => 'The Lord of the Rings',
              'author' => 'J.R.R. Tolkien',
              'genre_id' => 1,
              'year_published' => "1954",
              'stock' => 10
         ];
    
         $response = $this->json('POST', '/api/books', $body, [
              'Accept' => 'application/json',
              'Authorization' => 'Bearer ' . $token
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
        $token = $this->authenticate(true);
         $body = [
              'author' => 'J.R.R. Tolkien',
              'genre_id' => 1,
              'year_published' => "1954",
              'stock' => 10
         ];
    
         $response = $this->json('POST', '/api/books', $body, [
              'Accept' => 'application/json',
              'Authorization' => 'Bearer ' . $token
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
        $token = $this->authenticate(true);
         $body = [
            'title' => 'The Lord of the Rings',
              'genre_id' => 1,
              'year_published' => "1954",
              'stock' => 10
         ];
    
         $response = $this->json('POST', '/api/books', $body, [
              'Accept' => 'application/json',
              'Authorization' => 'Bearer ' . $token
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
        $token = $this->authenticate(true);
         $body = [
            'title' => 'The Lord of the Rings',
              'author' => 'J.R.R. Tolkien',
              'genre_id' => 1,
              'stock' => 10
         ];
    
         $response = $this->json('POST', '/api/books', $body, [
              'Accept' => 'application/json',
              'Authorization' => 'Bearer ' . $token
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
        $token = $this->authenticate(true);
         $body = [
            'title' => 'The Lord of the Rings',
              'author' => 'J.R.R. Tolkien',
              'genre_id' => 1,
              'year_published' => "1954",
         ];
    
         $response = $this->json('POST', '/api/books', $body, [
              'Accept' => 'application/json',
              'Authorization' => 'Bearer ' . $token
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

    public function test_get_book_librarian()
    {
        $token = $this->authenticate(true);
        $this->seed(GenreSeeder::class);
        $book = Book::factory()->create();
        $response = $this->json('GET', '/api/books/' . $book->id, [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
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

    public function test_get_book_student()
    {
        $token = $this->authenticate(false);
        $this->seed(GenreSeeder::class);
        $book = Book::factory()->create();
        $response = $this->json('GET', '/api/books/' . $book->id, [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
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
        $token = $this->authenticate(false);
        $book = Book::factory()->create();
        $response = $this->json('GET', '/api/books/'.($book->id + 1), [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
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

}
