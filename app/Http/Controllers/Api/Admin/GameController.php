<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data'   => Game::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string',
            'image'       => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        // Upload file di luar transaksi (filesystem bukan atomic)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('games', 'public');
        }

        try {
            DB::beginTransaction();

            $game = Game::create([
                'name'        => $request->name,
                'slug'        => Str::slug($request->name),
                'image'       => $imagePath,
                'description' => $request->description,
                'status'      => $request->status,
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Game berhasil ditambahkan',
                'data'    => $game,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file yang sudah terlanjur diupload
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            Log::error('GameController@store failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal menambahkan game. Silakan coba lagi.',
            ], 500);
        }
    }

    public function show($id)
    {
        return response()->json([
            'status' => true,
            'data'   => Game::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        $request->validate([
            'name'        => 'required|string',
            'image'       => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        $oldImage = $game->image;
        $newImagePath = null;
        $hasNewImage = $request->hasFile('image');

        // Upload file baru di luar transaksi
        if ($hasNewImage) {
            $newImagePath = $request->file('image')->store('games', 'public');
        }

        try {
            DB::beginTransaction();

            $game->update([
                'name'        => $request->name,
                'slug'        => Str::slug($request->name),
                'image'       => $hasNewImage ? $newImagePath : $game->image,
                'description' => $request->description,
                'status'      => $request->status,
            ]);

            DB::commit();

            // Hapus gambar lama setelah DB commit berhasil
            if ($hasNewImage && $oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Game berhasil diperbarui',
                'data'    => $game,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file baru yang sudah terlanjur diupload
            if ($newImagePath && Storage::disk('public')->exists($newImagePath)) {
                Storage::disk('public')->delete($newImagePath);
            }

            Log::error('GameController@update failed', ['game_id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal memperbarui game. Silakan coba lagi.',
            ], 500);
        }
    }

    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        $imagePath = $game->image;

        try {
            DB::beginTransaction();

            $game->delete();

            DB::commit();

            // Hapus file setelah DB commit berhasil
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Game berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('GameController@destroy failed', ['game_id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus game. Silakan coba lagi.',
            ], 500);
        }
    }
}
