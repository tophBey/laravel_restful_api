<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {

        $this->post('/api/users', data:[
            "username" => "arif",
            "password" => "rahasia",
            "name" => "arif",
        ])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => "arif",
                    "name" => "arif"
                ]

            ]);

    }

    public function testRegisterFail()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => '',
        ])->assertJson([
                    'errors' => [
                        'username' => [
                            "The username field is required."
                        ],
                        'password' => [
                            "The password field is required."
                        ],
                        'name' => [
                            "The name field is required."
                        ]
                    ]
                ])
            ->assertStatus(400);


    }
    public function testRegisterUsernameAlreadyExist()
    {

        $user = new User();
        $user->username = 'arif';
        $user->name = 'arif';
        $user->password = 'rahasia';
        $user->save();

        $this->post('/api/users', [
            "username" => "arif",
            "password" => "rahasia",
            "name" => "arif",
        ])
            ->assertStatus(400);

        self::assertEquals("arif", $user->username);
        self::assertEquals("arif", $user->name);
    }


    public function testloginSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [

            'username' => 'arif',
            'password' => 'rahasia'

        ])->assertJson([
                    'data' => [
                        'username' => 'arif',
                        'name' => 'arif'
                    ]
                ])->assertStatus(200);

        $user = User::where('username', 'arif')->first();

        $this->assertNotNull($user->token);

    }

    public function testloginFailedlUsernameNotFound()
    {

        $this->post('/api/users/login', [

            'username' => 'arif',
            'password' => 'rahasia'

        ])->assertJson([
                    'errors' => [
                        'message' => [
                            'username & password wrong'
                        ]
                    ]
                ])->assertStatus(401);

    }

    public function testloginFailPassword()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/users/login', [

            'username' => 'arif',
            'password' => 'salah'

        ])->assertJson([
                    'errors' => [
                        'message' => [
                            'username & password wrong'
                        ]
                    ]
                ])->assertStatus(401);


    }


    public function testGetSuccess()
    {

        $this->seed(UserSeeder::class);

        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'arif',
                    'name' => 'arif'

                ]
            ]);

        $user = User::where('username', 'arif')->first();
        $this->assertNotNull($user->token);
        $this->assertEquals('arif', $user->username);
        $this->assertEquals('arif', $user->name);

    }

    public function testGetUnauthorize()
    {

        $this->seed(UserSeeder::class);

        $this->get('/api/users/current')->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unautorize'
                    ]
                ]
            ]);


    }

    public function testGetTokenInvalid()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/users/current', [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unautorize'
                    ]
                ]
            ]);

    }


    public function testUpdatePasswordSucces()
    {

        $this->seed(UserSeeder::class);

        $oldUsers = User::where('username', 'arif')->first();

        $this->patch(
            '/api/users/current',
            [

                'password' => 'baru'

            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'arif',
                    'name' => 'arif'

                ]
            ]);

        // pastikan passwordnya tidak sama dengan sebelumnya
        $newUser = User::where('username', 'arif')->first();
        $this->assertNotEquals($newUser->password, $oldUsers->password);


    }

    public function testUpdateNameSucces()
    {
        $this->seed(UserSeeder::class);

        $oldUsers = User::where('username', 'arif')->first();

        $this->patch(
            '/api/users/current',
            [

                'name' => 'baru'

            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'arif',
                    'name' => 'baru'

                ]
            ]);

        $newUser = User::where('username', 'arif')->first();
        $this->assertNotEquals($newUser->name, $oldUsers->name);


    }

    public function testUpdateFailed()
    {

        $this->seed(UserSeeder::class);

        $oldUsers = User::where('username', 'arif')->first();

        $this->patch(
            '/api/users/current',
            [

                'name' => 'abcdefghijklmopqryshbhbwhmarcmarquezhwhbwgdwbnbwhbdhwhdbwdwhghdwhjhkhhkqhqdghqd
                hwgqdhgwhqdghgqdhgqdhghdghoiuytreoplkmjnhbvffghgqdhgqdhghdghoiuytreoplkmjnhbvffghgqdhgqdhghdghoiuytreoplkmjnhbvffghgqdhgqdhghdghoiuytreoplkmjnhbvff'

            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field must not be greater than 100 characters.'
                    ]
                    

                ]
            ]);
    }


    public function testLogoutSuccess(){

        $this->seed(UserSeeder::class);

        $this->delete('/api/users/logout', headers:[
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=> true
        ]);

        $user = User::where('username', 'arif')->first();
        $this->assertNull($user->token);
    }

    public function testLogoutFailed(){

        $this->seed(UserSeeder::class);

        $this->delete('/api/users/logout', headers:[
            'Authorization' => 'salah'
        ])->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => [
                    'unautorize'
                ]
            ]
        ]);
    }






}
