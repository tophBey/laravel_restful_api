<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username','arif')->first();
        Contact::create([
            'first_name' => 'zainal',
            'last_name'=> 'arif',
            'email'=> 'test@gmail.com',
            'phone' => '11111111',
            'user_id' => $user->id
        ]);

    }
}
