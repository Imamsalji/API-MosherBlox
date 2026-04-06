<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data'   => Product::with('game')->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_id'       => 'required|exists:games,id',
            'name'          => 'required|string',
            'price'         => 'required|numeric',
            'specification' => 'required|string',
            'image'         => 'nullable|image|max:2048',
            'stock'         => 'nullable|integer',
            'status'        => 'required|boolean',
        ]);

        // Upload file di luar transaksi
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        try {
            DB::beginTransaction();

            $product = Product::create([
                'game_id'       => $request->game_id,
                'name'          => $request->name,
                'price'         => $request->price,
                'specification' => $request->specification,
                'image'         => $imagePath,
                'stock'         => $request->stock,
                'status'        => $request->status,
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Produk berhasil ditambahkan',
                'data'    => $product,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file yang sudah terlanjur diupload
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            Log::error('ProductController@store failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal menambahkan produk. Silakan coba lagi.',
            ], 500);
        }
    }

    public function show($id)
    {
        return response()->json([
            'status' => true,
            'data'   => Product::with('game')->findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'game_id'       => 'required|exists:games,id',
            'name'          => 'required|string',
            'price'         => 'required|numeric',
            'specification' => 'required|string',
            'image'         => 'nullable|image|max:2048',
            'stock'         => 'nullable|integer',
            'status'        => 'required|boolean',
        ]);

        $oldImage = $product->image;
        $newImagePath = null;
        $hasNewImage = $request->hasFile('image');

        // Upload file baru di luar transaksi
        if ($hasNewImage) {
            $newImagePath = $request->file('image')->store('products', 'public');
        }

        try {
            DB::beginTransaction();

            $product->update([
                'game_id'       => $request->game_id,
                'name'          => $request->name,
                'price'         => $request->price,
                'specification' => $request->specification,
                'image'         => $hasNewImage ? $newImagePath : $product->image,
                'stock'         => $request->stock,
                'status'        => $request->status,
            ]);

            DB::commit();

            // Hapus gambar lama setelah DB commit berhasil
            if ($hasNewImage && $oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Produk berhasil diperbarui',
                'data'    => $product,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file baru yang sudah terlanjur diupload
            if ($newImagePath && Storage::disk('public')->exists($newImagePath)) {
                Storage::disk('public')->delete($newImagePath);
            }

            Log::error('ProductController@update failed', ['product_id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal memperbarui produk. Silakan coba lagi.',
            ], 500);
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $imagePath = $product->image;

        try {
            DB::beginTransaction();

            $product->delete();

            DB::commit();

            // Hapus file setelah DB commit berhasil
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Produk berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ProductController@destroy failed', ['product_id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus produk. Silakan coba lagi.',
            ], 500);
        }
    }
}
