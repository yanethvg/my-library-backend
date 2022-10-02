<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    // refresh database
    use RefreshDatabase;

    protected $token;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->token = $this->getToken();

    }

    public function getToken(){
        User::create([
            'first_name'=> 'Joe',
            'last_name'=> 'Doe',
            'password' => 'testtest',
            'email' => 'joeDoe@test.com',
            'password' => Hash::make('testtest')
        ])->assignRole('librarian');

        User::create([
            'first_name'=> 'Dan',
            'last_name'=> 'Doe',
            'password' => 'testtest',
            'email' => 'danDoe@test.com',
            'password' => Hash::make('testtest')
        ])->assignRole('student');

        $body =[
            'email' => 'joeDoe@test.com',
            'password' => 'testtest'
        ];

        $response =$this->json('POST','/api/login',$body,['Accept' => 'application/json']);

        return $response->json('access_token');
    }

    public function test_api_register() {
        $this->withoutExceptionHandling();
       
        $body = [
            'first_name'=> 'test',
            'last_name'=> 'test',
            'email' => 'test@test.com',
            'password' => 'testtest',
            "password_confirmation" => 'testtest',
            'role_id' => 2
        ];
        
        $response =$this->json('POST','/api/register',
            $body,
            ['Accept' => 'application/json',  'Authorization' => 'Bearer ' . $this->token]
        );
       
        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'role',
                'created_at',
                'updated_at'
            ]
        ]);
    }
    public function test_wrong_api_register_without_permission_users_store()
    {

        $body =[
            'email' => 'danDoe@test.com',
            'password' => 'testtest'
        ];

        $response =$this->json('POST','/api/login',$body,['Accept' => 'application/json']);

        $token = $response->json('access_token');
       
        $body = [
            'first_name'=> 'test',
            'last_name'=> 'test',
            'email' => 'test@test.com',
            'password' => 'testtest',
            "password_confirmation" => 'testtest',
            'role_id' => 2
        ];
        
        $response =$this->json('POST','/api/register',
            $body,
            ['Accept' => 'application/json',  'Authorization' => 'Bearer ' . $token]
        );
       
        $response->assertStatus(403);
        $response->assertJsonStructure(['message']);
    }
    public function test_wrong_api_register_without_email() {
       
        $body = [
            'first_name'=> 'test',
            'last_name'=> 'test',
            'password' => 'testtest',
            "password_confirmation" => 'testtest',
            'role_id' => 2
        ];
        
        $response =$this->json('POST','/api/register',
            $body,
            ['Accept' => 'application/json',  'Authorization' => 'Bearer ' . $this->token]
        );
       
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['email']);
    }
    public function test_wrong_api_register_without_password_confirmation() 
    {
        $body = [
            'first_name'=> 'test',
            'last_name'=> 'test',
            'email' => 'test@test.com',
            'password' => 'testtest',
            'role_id' => 2
        ];
        
        $response =$this->json('POST','/api/register',
            $body,
            ['Accept' => 'application/json',  'Authorization' => 'Bearer ' . $this->token]
        );
       
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['password']);
    }
    public function test_wrong_api_register_without_first_name() {

        $body = [
            'last_name'=> 'test',
            'password' => 'testtest',
            'email' => 'test@test.com',
            "password_confirmation" => 'testtest',
            'role_id' => 2
        ];
        
        $response =$this->json('POST','/api/register',
            $body,
            ['Accept' => 'application/json',  'Authorization' => 'Bearer ' . $this->token]
        );
       
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['first_name']);
    }
    public function test_wrong_api_register_without_last_name() {
        $body = [
            'first_name'=> 'test',
            'password' => 'testtest',
            'email' => 'test@test.com',
            "password_confirmation" => 'testtest',
            'role_id' => 2
        ];
        
        $response =$this->json('POST','/api/register',
            $body,
            ['Accept' => 'application/json',  'Authorization' => 'Bearer ' . $this->token]
        );
       
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['last_name']);
    }

    public function test_wrong_api_register_without_role_id()
    {
        $body = [
            'first_name'=> 'test',
            'last_name'=> 'test',
            'password' => 'testtest',
            'email' => 'test@test.com',
            "password_confirmation" => 'testtest',
        ];
        
        $response =$this->json('POST','/api/register',
            $body,
            ['Accept' => 'application/json',  'Authorization' => 'Bearer ' . $this->token]
        );
       
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['role_id']);
    }
    public function test_wrong_api_register_without_information() {
       
        $body = [
        ];
        
        $response =$this->json('POST','/api/register',
            $body,
            ['Accept' => 'application/json',  'Authorization' => 'Bearer ' . $this->token]
        );
       
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['first_name','last_name','email','password']);
    }
    public function test_wrong_api_register_email_taking() {
       
        $body = [
            'first_name'=> 'Joe',
            'last_name'=> 'Doe',
            'password' => 'testtest',
            'email' => 'joeDoe@test.com',
            "password_confirmation" => 'testtest',
            'role_id' => 2
        ];
        
        $response =$this->json('POST','/api/register',
            $body,
            ['Accept' => 'application/json',  'Authorization' => 'Bearer ' . $this->token]
        );
       
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['email']);
    }

    public function test_api_login(){
        $this->withoutExceptionHandling();
        $body = [
            'email' => 'joeDoe@test.com',
            'password' => 'testtest'
        ];
        $response = $this->json('POST','/api/login',$body,['Accept' => 'application/json']);
       
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token', 
            'token_type',
            'user' => [
                'full_name',
                'email',
            ],
            'role',
            'permissions'
        ]);
    }
    public function test_wrong_api_login_without_email() {
       
        $body = [
            'password' => 'testtest'
        ];
        
        $response =$this->json('POST','/api/login',$body,['Accept' => 'application/json']);
       
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['email']);
    }
    public function test_wrong_api_login_without_password() {
       
        $body = [
            'email' => 'joeDoe@test.com',
        ];
        
        $response =$this->json('POST','/api/login',$body,['Accept' => 'application/json']);
       
        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['password']);
    }

    public function test_api_logout() {
        $this->withoutExceptionHandling();
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            ])->json('POST','/api/logout');
       
        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
    }

    public function test_wrong_api_logout_unautorized() {
        //creating access token
        $token = "swqehd12ye1273782136";
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            ])->json('POST','/api/logout');
       
        $response->assertUnauthorized();
        $response->assertJsonStructure(['message']);
    }

    public function test_get_all_users() {
        $this->withoutExceptionHandling();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
            ])->json('GET','/api/users');
       
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
                    'updated_at',
                ]
            ]
        ]);
    }

    public function test_wrong_get_all_user_without_autentication()
    {
        $response = $this->json('GET','/api/users');
       
        $response->assertUnauthorized();
        $response->assertJsonStructure(['message']);
    }

}
