<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function welcome()
    {
        return view('welcome', [
            'posts' => Post::latest()->paginate(9)
        ]);
    }

    public function index()
    {
        return view('posts.index', [
            'posts' => Post::filter(request(['search']))->get()
        ]);
    }

    public function show(Post $post)
    {
        return view('posts.show', [
            'post' => $post,
            'related_post' => Post::getRelatedPost($post)
        ]);
    }
}
