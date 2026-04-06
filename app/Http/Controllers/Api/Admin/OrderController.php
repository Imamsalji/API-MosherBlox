<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            'status'  => true,
            'message' => 'List semua order',
            'data'    => $orders,
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
            'status'  => true,
            'message' => 'Detail order',
            'data'    => $order,
        ]);
    }

    /**
     * PUT /admin/orders/{id}/verify
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'status'      => 'required|in:success,rejected',
            'admin_note'  => 'required|string',
            'bukti_admin' => 'nullable|image|max:2048',
        ]);

        $order = Order::findOrFail($id);

        // Upload file di luar transaksi
        $imagePath = null;
        if ($request->hasFile('bukti_admin')) {
            $imagePath = $request->file('bukti_admin')->store('games', 'public');
        }

        try {
            DB::beginTransaction();

            $order->update([
                'status'      => $request->status,
                'admin_note'  => $request->admin_note,
                'bukti_admin' => $imagePath ?? $order->bukti_admin,
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Order berhasil diverifikasi',
                'data'    => $order,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file baru yang sudah terlanjur diupload
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            Log::error('OrderController@verify failed', ['order_id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal memverifikasi order. Silakan coba lagi.',
            ], 500);
        }
    }
}
