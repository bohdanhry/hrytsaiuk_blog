<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $categories = BlogCategory::paginate($perPage);
        return response()->json($categories);
    }

    public function show($id)
    {
        $category = BlogCategory::find($id);
        if (!$category) {
            return response()->json(['error' => 'Категорія не знайдена'], 404);
        }
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug',
            'parent_id' => 'nullable|exists:blog_categories,id',
            'description' => 'nullable|string',
        ]);
        $slug = $request->slug ?: Str::slug($request->title);
        $category = BlogCategory::create([
            'title' => $request->title,
            'slug' => $slug,
            'parent_id' => $request->parent_id,
            'description' => $request->description ?? '',
        ]);
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = BlogCategory::find($id);
        if (!$category) {
            return response()->json(['error' => 'Категорія не знайдена'], 404);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:blog_categories,id',
            'description' => 'nullable|string',
        ]);
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        $category->update($validated);
        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = BlogCategory::find($id);
        if (!$category) {
            return response()->json(['error' => 'Категорія не знайдена'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Категорія видалена']);
    }
}
