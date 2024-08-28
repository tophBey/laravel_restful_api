<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Address;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
   
    public function testCreateAddressSucces(){

        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::where('first_name','zainal')->first();

        $this->post("/api/contacts/$contact->id/addresses",data:[
            "street" => "mauk",
            "city" => "Tangerang",
            "province" => "banten",
            "country" => "indonesia",
            "postal_code" => "indonesia"
        ],
        headers: [
            "Authorization" => 'test'
        ])->assertStatus(201)->assertJson([
            'data' => [
                "street" => "mauk",
                "city" => "Tangerang",
                "province" => "banten",
                "country" => "indonesia",
                "postal_code" => "indonesia"
            ]
        ]);

    }

    public function testCreateAddressFail(){

        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::where('first_name','zainal')->first();

        $this->post("/api/contacts/$contact->id/addresses",data:[
            "street" => "mauk",
            "city" => "Tangerang",
            "province" => "banten",
            "country" => "",
            "postal_code" => "indonesia"
        ],
        headers: [
            "Authorization" => 'test'
        ])->assertStatus(400)->assertJson([
            'errors' => [
                'country'=> [
                    'The country field is required.'
                ]
            ]
        ]);  
    }

    public function testCreateAddressNotFound(){

        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::where('first_name','zainal')->first();

        $this->post('/api/contacts/' . $contact->id  + 1 . '/addresses',data:[
            "street" => "mauk",
            "city" => "Tangerang",
            "province" => "banten",
            "country" => "Indonesia",
            "postal_code" => "indonesia"
        ],
        headers: [
            "Authorization" => 'test'
        ])->assertStatus(404)->assertJson([
            'error' => [
               'message' => [
                'not found'
               ]
            ]
        ]);  
    }


    public function testGetSucces(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();


        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id ,headers:[
            'Authorization' => 'test'
        ] )->assertStatus(200)->assertJson([
            'data' => [
                'street' => "test",
                "city"=> "test",
                "province"=> "test",
                "country"=> "test",
                "postal_code"=> "test"
            ]
        ]);

    }

    public function testGetFailed(){

        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();


        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id +1 ,headers:[
            'Authorization' => 'test'
        ] )->assertStatus(404)->assertJson([
            "error" => [
                "message"=> [
                    "not found"
                ]
            ]
        ]);
    }

    public function testUpdateSuccess(){

        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();


        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
        data:[
                'street' => "test1",
                "city"=> "test1",
                "province"=> "test1",
                "country"=> "test1",
                "postal_code"=> "test1"
        ] ,
        headers:[
            'Authorization' => 'test'
        ] )->assertStatus(200)->assertJson([
            'data' => [
                'street' => "test1",
                "city"=> "test1",
                "province"=> "test1",
                "country"=> "test1",
                "postal_code"=> "test1"
            ]
        ]);

        $addressNew = Address::query()->limit(1)->first();

        $this->assertNotEquals($addressNew->street, $address->street);
        $this->assertNotEquals($addressNew->city, $address->city);
        $this->assertNotEquals($addressNew->country, $address->country);
        $this->assertNotEquals($addressNew->province, $address->province);
        $this->assertNotEquals($addressNew->postal_code, $address->postal_code);

    }

    public function testUpdateFail(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();


        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id ,
        data:[
                'street' => "test1",
                "city"=> "test1",
                "province"=> "test1",
                "country"=> "",
                "postal_code"=> "test1"
        ],
        
        headers:[
            'Authorization' => 'test'
        ] )->assertStatus(400)->assertJson([
            'errors'=>[
                "country" => [
                    "The country field is required."
                ]
            ]
        ]);
    }

    public function testUpdateNotFound(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();


        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id +1,
        data:[
            'street' => "test1",
            "city"=> "test1",
            "province"=> "test1",
            "country"=> "indonesia",
            "postal_code"=> "test1"
        ]
        ,headers:[
            'Authorization' => 'test'
        ] )->assertStatus(404)->assertJson([
            "error" => [
                "message"=> [
                    "not found"
                ]
            ]
        ]);
    }


    public function testDeleteSuccess(){
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();


        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id ,headers:[
            'Authorization' => 'test'
        ] )->assertStatus(200)->assertJson([
            'data' => true
        ]);

        $this->assertEmpty(Address::query()->where('id', $address->id)->get());

    }

    public function testDeleteFailed(){

        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();


        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1 ,headers:[
            'Authorization' => 'test'
        ] )->assertStatus(404)->assertJson([
            'error' => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
    }


    public function testListFailed(){

        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id + 1 . '/addresses', headers:[
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'error' => [
                  'message' => [
                    'not found'
                  ]
                ]
            ]);
    }

    public function testListSuccess(){

        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id . '/addresses', headers:[
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                   [
                    'street' => 'test',
                    'province' => 'test',
                    'country' => 'test',
                    'postal_code' => 'test',
                    'city' => 'test',
                   ]
                ]
            ]);

    }
 }


