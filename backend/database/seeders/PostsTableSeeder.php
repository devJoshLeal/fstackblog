<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('posts')->insert([
            [
                'title' => 'The Future of Technology',
                'content' => 'Content about the future of technology...',
                'image' => null,
                'category_id' => 1, // Asegúrate de que este ID exista en la tabla categories
                'user_id' => 1, // Asegúrate de que este ID exista en la tabla users
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Healthy Living Tips',
                'content' => 'Content about healthy living...',
                'image' => null,
                'category_id' => 2,
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
