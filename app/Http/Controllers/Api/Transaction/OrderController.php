<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Riwayat pesanan',
            'data' => $orders
        ]);
    }

    public function show($id)
    {
        $order = Order::with('items.product')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'message' => 'Detail pesanan',
            'data' => $order
        ]);
    }

    /**
     * POST /checkout
     */
    public function checkout(Request $request)
    {
        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Keranjang kosong'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($cart->items as $item) {
                $total += $item->price * $item->qty;
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $total,
                'status' => 'pending'
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'qty' => $item->qty,
                    'price' => $item->price
                ]);
            }

            // Kosongkan cart
            $cart->items()->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Checkout berhasil',
                'data' => [
                    'order_id' => $order->id,
                    'total_price' => $total,
                    'status' => $order->status
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Checkout gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadPayment(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:3072'
        ]);

        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        // Simpan file
        $path = $request->file('payment_proof')
            ->store('payments', 'public');

        // Update order
        $order->update([
            'payment_proof' => $path,
            'status' => 'waiting_verification'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Bukti pembayaran berhasil diupload',
            'data' => [
                'order_id' => $order->id,
                'status' => $order->status
            ]
        ]);
    }

    public function getRoblox($user)
    {
        try {

            // 1️⃣ Ambil user ID dari username
            $userRes = Http::post(
                'https://users.roblox.com/v1/usernames/users',
                [
                    "usernames" => [$user],
                    "excludeBannedUsers" => false
                ]
            );

            if (!$userRes->ok()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal ambil user'
                ], 500);
            }

            $userData = $userRes->json('data.0');

            if (!$userData) {
                return response()->json([
                    'status' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $userId = $userData['id'];

            // 2️⃣ Ambil avatar
            $thumbRes = Http::get(
                'https://thumbnails.roblox.com/v1/users/avatar-headshot',
                [
                    'userIds' => $userId,
                    'size' => '420x420',
                    'format' => 'Png'
                ]
            );

            if (!$thumbRes->ok()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal ambil avatar'
                ], 500);
            }

            $avatar = $thumbRes->json('data.0.imageUrl');

            return response()->json([
                'status' => true,
                'message' => 'Avatar berhasil didapatkan',
                'data' => $avatar,
                'user' => $user
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
