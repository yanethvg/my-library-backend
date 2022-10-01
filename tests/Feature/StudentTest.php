<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\GenreSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\BookSeeder;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;

class StudentTest extends TestCase
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
       $this->seed(GenreSeeder::class);
       $this->seed(BookSeeder::class);
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

   protected function authenticate($librarian)
   {
       
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

    public function test_get_all_students()
    {
        $token = $this->authenticate(true);
        $response = $this->json('GET','/api/students',[],['Accept' => 'application/json','Authorization' => 'Bearer '.$token]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'role',
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

    public function test_wrong_get_all_students_wihout_permission_students_index()
    {
        $token = $this->authenticate(false);
        $response = $this->json('GET','/api/students',[],['Accept' => 'application/json','Authorization' => 'Bearer '.$token]);
        $response->assertStatus(403);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_book_can_be_borrow()
    {
        $token = $this->authenticate(false);
        $book = Book::first();
        
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
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
        $book = Book::first();
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
        $token = $this->authenticate(false);
        $book = Book::first();
        $book->stock = 0;
        $book->save();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->json('POST', '/api/books/'.$book->id.'/borrow');

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_book_can_not_be_borrow_to_user_borrowed()
    {
        $token = $this->authenticate(false);
        $book = Book::first();
        $book->users()->attach($this->student->id);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->json('POST', '/api/books/'.$book->id.'/borrow');

        $response->assertStatus(400);
       
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_book_can_be_return()
    {
        $token = $this->authenticate(true);
        $book = Book::first();
        $book->users()->attach($this->student->id);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->json('POST', '/api/books/'.$book->id.'/'. $this->student->id.'/return');

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

    public function test_book_can_not_be_return_not_borrowed_before()
    {
        $token = $this->authenticate(true);
        $book = Book::first();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->json('POST', '/api/books/'.$book->id.'/'. $this->student->id.'/return');


        $response->assertStatus(400);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_book_can_be_return_without_permission_return()
    {
        $token = $this->authenticate(false);
        $book = Book::first();
        $book->users()->attach($this->student->id);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->json('POST', '/api/books/'.$book->id.'/'. $this->student->id.'/return');

        $response->assertStatus(403);
        $response->assertJsonStructure([
            'message'
        ]);
    }


}
