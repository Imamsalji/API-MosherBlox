<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Game::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'status' => 'required|boolean'
        ]);

        $image = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')
                ->store('games', 'public');
        }

        $game = Game::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $image,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Game berhasil ditambahkan',
            'data' => $game
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'status' => true,
            'data' => Game::findOrFail($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'status' => 'required|boolean'
        ]);

        if ($request->hasFile('image')) {
            if ($game->image) {
                Storage::disk('public')->delete($game->image);
            }
            $game->image = $request->file('image')
                ->store('games', 'public');
        }

        $game->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Game berhasil diperbarui',
            'data' => $game
        ]);
    }

    public function destroy($id)
    {
        $game = Game::findOrFail($id);

        if ($game->image) {
            Storage::disk('public')->delete($game->image);
        }

        $game->delete();

        return response()->json([
            'status' => true,
            'message' => 'Game berhasil dihapus'
        ]);
    }
}
