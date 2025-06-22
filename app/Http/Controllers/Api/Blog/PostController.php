<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $posts = BlogPost::with(['user', 'category'])->paginate($perPage);

        return response()->json($posts);
    }
    public function show($id)
    {
        $post = BlogPost::with(['user:id,name', 'category:id,title'])->find($id);

        if (!$post) {
            return response()->json(['error' => 'Пост не знайдено'], 404);
        }

        return response()->json($post);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content_raw' => 'required|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $post = BlogPost::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'category_id' => $request->category_id,
            'excerpt' => $request->excerpt ?? '',
            'content_raw' => $request->content_raw,
            'is_published' => $request->is_published ?? false,
            'published_at' => $request->published_at,
        ]);

        $post->user_id = Auth::id() ?? BlogPost::UNKNOWN_USER;
        $post->content_html = null; // Тут можна додати конвертацію content_raw в HTML
        $post->save();

        return response()->json($post, 201);
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::find($id);
        if (!$post) {
            return response()->json(['error' => 'Пост не знайдено'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string',
            'slug' => 'nullable|string',
            'category_id' => 'nullable|integer|exists:blog_categories,id',
            'excerpt' => 'nullable|string',
            'content_raw' => 'required|string',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $validated['content_html'] = $validated['content_raw'];

        $post->update($validated);

        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = BlogPost::find($id);

        if (!$post) {
            return response()->json(['error' => 'Пост не знайдено'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Пост видалено']);
    }
}
