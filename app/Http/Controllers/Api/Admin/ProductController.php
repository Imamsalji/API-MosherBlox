<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Product::with('game')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'specification' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'stock' => 'nullable|integer',
            'status' => 'required|boolean'
        ]);

        $image = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')
                ->store('products', 'public');
        }

        $product = Product::create([
            'game_id' => $request->game_id,
            'name' => $request->name,
            'price' => $request->price,
            'specification' => $request->specification,
            'image' => $image,
            'stock' => $request->stock,
            'status' => $request->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => $product
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'status' => true,
            'data' => Product::with('game')->findOrFail($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'game_id' => 'required|exists:games,id',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'specification' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'stock' => 'nullable|integer',
            'status' => 'required|boolean'
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')
                ->store('products', 'public');
        }

        $product->update([
            'game_id' => $request->game_id,
            'name' => $request->name,
            'price' => $request->price,
            'specification' => $request->specification,
            'stock' => $request->stock,
            'status' => $request->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil diperbarui',
            'data' => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }
}
