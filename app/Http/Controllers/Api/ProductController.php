<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /products/{id}
     */
    public function show($id)
    {
        $product = Product::with('game')
            ->where('status', 1)
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => 'Detail produk',
            'data' => $product
        ]);
    }
}
