<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use App\Models\User;
use Tests\TestCase;

class RoleTest extends TestCase
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

    public function test_get_all_roles()
    {
        $token = $this->authenticate(true);
        $response = $this->json('GET', '/api/roles', [], [
             'Authorization' => 'Bearer ' . $token,
             'Accept' => 'application/json'
        ]);
   
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

    public function test_wrong_get_all_roles_wihout_permission_role_index()
    {
        $token = $this->authenticate(false);
        $response = $this->json('GET', '/api/roles', [], [
             'Authorization' => 'Bearer ' . $token,
             'Accept' => 'application/json'
        ]);
   
        $response->assertStatus(403);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_wrong_get_all_roles_without_authorization()
    {
        $response = $this->json('GET', '/api/roles', [], [
             'Accept' => 'application/json'
        ]);
   
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
    }
}
