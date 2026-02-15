<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * GET /cart
     * View cart user
     */
    public function index()
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', auth()->id())
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Keranjang kosong',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data keranjang',
            'data' => $cart
        ]);
    }

    /**
     * POST /cart/add
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1'
        ]);

        $product = Product::where('status', 1)
            ->findOrFail($request->product_id);

        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id()
        ]);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->update([
                'qty' => $item->qty + $request->qty
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'qty' => $request->qty,
                'price' => $product->price
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Produk ditambahkan ke keranjang',
            'data' => [
                'id' => $request->product_id,
                'qty' => $request->qty,
            ]
        ]);
    }

    /**
     * PUT /cart/{id}
     * Update qty
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            // 'qty' => 'required|integer|min:1',
            'kondisi' => 'required'
        ]);

        $item = CartItem::where('id', $id)
            ->whereHas('cart', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->firstOrFail();

        if ($request->kondisi == "increment") {
            $item->increment('qty');
        } else {
            $item->decrement('qty');
        }

        return response()->json([
            'status' => true,
            'message' => 'Qty berhasil diperbarui'
        ]);
    }

    /**
     * DELETE /cart/{id}
     */
    public function remove($id)
    {
        $item = CartItem::where('id', $id)
            ->whereHas('cart', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->firstOrFail();

        $item->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item dihapus dari keranjang'
        ]);
    }
}
