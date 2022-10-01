<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Database\Seeders\GenreSeeder;
use App\Models\User;

class GenreTest extends TestCase
{
    // refresh database
    use RefreshDatabase;

    protected $token;

    public function setUp(): void
    {
        parent::setUp();
        $this->token = $this->authenticate();
        $this->seed(GenreSeeder::class);
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

    public function test_get_all_genres()
    {
        $this->withoutExceptionHandling();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            ])->json('get', '/api/genres', ['Accept' => 'application/json']);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ]
            ]
        ]);

    }
}
