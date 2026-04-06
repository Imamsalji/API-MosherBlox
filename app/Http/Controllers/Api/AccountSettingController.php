<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AccountSettingController extends Controller
{
    // 1. Get Profile
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => true,
            'data' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'avatar' => $user->avatar
                    ? asset('storage/' . $user->avatar)
                    : null,
            ],
        ]);
    }

    // 2. Update Name & Email
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        try {
            DB::beginTransaction();

            $user->update($validated);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Profile berhasil diupdate',
                'data'    => $user,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('updateProfile failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengupdate profile. Silakan coba lagi.',
            ], 500);
        }
    }

    // 3. Update Password
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Password lama salah',
            ], 400);
        }

        try {
            DB::beginTransaction();

            $user->update([
                'password' => Hash::make($validated['new_password']),
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Password berhasil diubah',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('updatePassword failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengubah password. Silakan coba lagi.',
            ], 500);
        }
    }

    // 4. Update Avatar
    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $oldAvatar = $user->avatar;

        // Simpan file baru terlebih dahulu (di luar transaksi karena operasi filesystem)
        $file    = $request->file('avatar');
        $newPath = $file->store('avatar', 'public');

        try {
            DB::beginTransaction();

            $user->update(['avatar' => $newPath]);

            DB::commit();

            // Hapus avatar lama hanya setelah DB commit berhasil
            if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
                Storage::disk('public')->delete($oldAvatar);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Avatar berhasil diupdate',
                'data'    => [
                    'avatar_url' => asset('storage/' . $newPath),
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file baru yang sudah terlanjur diupload karena DB gagal
            if (Storage::disk('public')->exists($newPath)) {
                Storage::disk('public')->delete($newPath);
            }

            Log::error('updateAvatar failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengupdate avatar. Silakan coba lagi.',
            ], 500);
        }
    }
}
