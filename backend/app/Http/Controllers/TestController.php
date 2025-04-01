<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;


class TestController extends Controller
{
    public function testOrm(){
        $categories = Category::all();
        foreach($categories as $category){
            echo "<h2>".$category->name . "</h2>";
            foreach($category->posts as $post){
                echo "<h3>". $post->title . "</h3>";
                echo "<p>". $post->user->name." ".$post->user->surname . "</p>";
                echo "<p>". $post->category->name . "</p>";
                echo "<p>". $post->content . "</p>";
            }
            echo "<hr>";
        }
    }
}
