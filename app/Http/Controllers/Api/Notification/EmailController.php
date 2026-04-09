<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = [
                'title' => $request->input('title'),
                'message' => $request->input('msg'),
            ];

            Mail::to($request->input('To'))->send(new SendEmail($data));

            return response()->json([
                'status' => true,
                'message' => 'Email berhasil dikirim'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Email gagal dikirim',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
