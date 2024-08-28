<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index()
    {
        return Bookmark::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id'
        ]);

        $bookmark = Bookmark::create($validated);
        return response()->json($bookmark, 201);
    }

    public function show($id)
    {
        return Bookmark::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'exists:users,id',
            'post_id' => 'exists:posts,id'
        ]);

        $bookmark = Bookmark::findOrFail($id);
        $bookmark->update($validated);
        return response()->json($bookmark);
    }

    public function destroy($id)
    {
        Bookmark::destroy($id);
        return response()->json(null, 204);
    }
}
