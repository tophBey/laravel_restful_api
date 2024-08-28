<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{

    public function testCreateSuccess(){
 
        $this->seed(UserSeeder::class);

        $this->post('/api/contacts', data:[
            'first_name' => 'zainal',
            'last_name'=> 'arif',
            'email'=> 'arif@gmail.com',
            'phone'=> '0888888888',

        ],
        headers: [
            'Authorization' => 'test'
        ]
        )->assertStatus(201)->assertJson([
            'data' =>[
                'first_name'=> 'zainal',
                'last_name'=> 'arif',
                'email'=> 'arif@gmail.com',
                'phone'=> '0888888888',
            ]
        ]);
    }

    public function testCreateFail(){

        $this->seed(UserSeeder::class);

        $this->post('/api/contacts', data:[
            'first_name' => '',
            'last_name'=> 'arif',
            'email'=> 'arif@gmail.com',
            'phone'=> '0888888888',

        ],
        headers: [
            'Authorization' => 'test'
        ]
        )->assertStatus(400)->assertJson([
            'errors' => [
                'first_name' => [
                    'The first name field is required.'
                ]
            ]
        ]);
    }



    public function testGetSuccess(){

        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id,headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' => [
                'first_name' => 'zainal',
                'last_name' => 'arif',
                'email' => 'test@gmail.com',
                'phone' => '11111111'
            ]

        ]);
    }

    public function testGetFail(){

        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id + 1, headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->assertJson([
            'error' => [
                'message' => [
                    'not found'
                ]
            ]

        ]);

    }

    public function testGetOtherUserContact(){

        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, headers: [
            'Authorization' => 'test2'
        ])->assertStatus(404)->assertJson([
            
            'error' => [
                'message' => [
                    'not found'
                ]
            ]

        ]);

    }

    public function testUpdateSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);


        $contact = Contact::query()->limit(1)->first();

        $user = User::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id,data:[
            'first_name'=> 'test3',
            'last_name'=> 'test3',
            'email'=> 'test3@gmail.com',
            'phone'=> '222222'
        ],headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' => [
                'first_name' => 'test3',
                'last_name' => 'test3',
                'email' => 'test3@gmail.com',
                'phone' => '222222'
            ]
        ]);

        $contactNew = Contact::query()->limit(1)->first();

        $this->assertNotEquals($contact->first_name, $contactNew->first_name);
        $this->assertNotEquals($contact->last_name, $contactNew->last_name);
        $this->assertNotEquals($contact->email, $contactNew->email);
        $this->assertNotEquals($contact->phone, $contactNew->phone);

        $this->assertNotNull($user->token);
    }

    public function testUpdateFail(){

        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id,data:[
            'first_name'=> '',
            'last_name'=> 'test3',
            'email'=> 'test3@gmail.com',
            'phone'=> '222222'
        ],headers: [
            'Authorization' => 'test'
        ])->assertStatus(400)->assertJson([
            'errors' => [
                'first_name' => [
                    'The first name field is required.'
                ]
            ]
        ]);
    }

    public function testDeleteSuccess(){

         $this->seed([UserSeeder::class, ContactSeeder::class]);
         $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . $contact->id,data:[],headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' => true
        ]);
        $this->assertEmpty(Contact::query()->where('id', $contact->id)->first());
    }

    public function testDeleteNotFpund(){
        $this->seed([UserSeeder::class, ContactSeeder::class]);
         $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . $contact->id+1,data:[],headers: [
            'Authorization' => 'test'
        ])->assertStatus(404)->assertJson([
            'error' => [
                "message" => [
                    'not found'
                ]
            ]
        ]);
    }


    public function testSearchByName(){
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?name=test",headers:[
            'Authorization' => 'test'
        ])->assertStatus(200)->json();

        Log::info(json_encode($response));


        $this->assertEquals(10, count($response['data']));
        $this->assertEquals(20, $response['meta']['total']);


    }

    public function testSearchByEmail(){
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?email=test",headers:[
            'Authorization' => 'test'
        ])->assertStatus(200)->json();

        Log::info(json_encode($response));


        $this->assertEquals(10, count($response['data']));
        $this->assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByPhone(){

        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?phone=111",headers:[
            'Authorization' => 'test'
        ])->assertStatus(200)->json();

        Log::info(json_encode($response));


        $this->assertEquals(10, count($response['data']));
        $this->assertEquals(20, $response['meta']['total']);

    }

    public function testSearchByWithPage(){

        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?size=5&page=2",headers:[
            'Authorization' => 'test'
        ])->assertStatus(200)->json();

        Log::info(json_encode($response));


        $this->assertEquals(5, count($response['data']));
        $this->assertEquals(20, $response['meta']['total']);

    }
}
