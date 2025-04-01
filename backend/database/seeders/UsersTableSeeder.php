<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'john@example.com',
                'password' => hash ('sha256', 'test1234'),
                'role' => 'admin',
                'description' => 'Administrator user',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane',
                'surname' => 'Smith',
                'email' => 'jane@example.com',
                'password' => hash ('sha256', 'test1234'),
                'role' => 'user',
                'description' => 'Regular user',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fulano',
                'surname' => 'Silva',
                'email' => 'fulanos@example.com',
                'password' => hash ('sha256', 'test1234'),
                'role' => 'user',
                'description' => 'Regular user',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Beltrano',
                'surname' => 'Santos',
                'email' => 'beltranos@example.com',
                'password' => hash ('sha256', 'test1234'),
                'role' => 'user',
                'description' => 'Regular user',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name' => 'Ciclano',
                'surname' => 'Souza',
                'email' => 'ciclanos@example.com',
                'password' => hash ('sha256', 'test1234'),
                'role' => 'user',
                'description' => 'Regular user',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),

            ]
        ]);
    }
}
