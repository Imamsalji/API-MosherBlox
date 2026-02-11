<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * GET /admin/orders
     */
    public function index()
    {
        $orders = Order::with('items.product', 'user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'List semua order',
            'data' => $orders
        ]);
    }

    /**
     * GET /admin/orders/{id}
     */
    public function show($id)
    {
        $order = Order::with('items.product', 'user')
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => 'Detail order',
            'data' => $order
        ]);
    }

    /**
     * PUT /admin/orders/{id}/verify
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:success,rejected',
            'admin_note' => 'required|string'
        ]);

        $order = Order::where('status', 'waiting_verification')
            ->findOrFail($id);

        $order->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order berhasil diverifikasi',
            'data' => $order
        ]);
    }
}
