<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ], [
            'title.required' => 'Title is required',
            'content.required' => 'Content is required',
            'category_id.required' => 'Category ID is required',
            'category_id.exists' => 'The selected category does not exist',
            'image.image' => 'The file must be an image',
            'image.max' => 'The image must not be greater than 2MB',
        ]);

        $post = Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $media = $post->addMedia($request->file('image'))->toMediaCollection('images');
            $imageUrl = $media->getUrl();
        }

        $response = [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'category_id' => $post->category_id,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'image_url' => $imageUrl,
        ];

        return response()->json($response, 201);
    }
    public function show($id)
    {
        return Post::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'string',
            'content' => 'string',
            'category_id' => 'exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $post = Post::findOrFail($id);
        $post->update($validated);
        if ($request->hasFile('image')) {
            $post->clearMediaCollection('images');
            $post->addMedia($request->file('image'))->toMediaCollection('images');
        }

        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        $post->clearMediaCollection('images');

        $post->delete();

        return response()->json(null, 204);
    }
}
