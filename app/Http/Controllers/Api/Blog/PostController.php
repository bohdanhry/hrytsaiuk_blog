<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        // Кількість елементів на сторінку (по замовчуванню 10)
        $perPage = $request->input('per_page', 10);

        // Завантаження постів з автором і категорією
        $posts = BlogPost::with(['user', 'category'])->paginate($perPage);

        // Повертаємо JSON з пагінацією
        return response()->json($posts);
    }
}
