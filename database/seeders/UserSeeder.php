<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "username"=> "arif",
            'password' =>Hash::make('rahasia'),
            'name' => 'arif',
            'token' => 'test'

        ]);

        User::create([
            "username"=> "test2",
            'password' =>Hash::make('rahasia'),
            'name' => 'test2',
            'token' => 'test2'

        ]);
    }
}
