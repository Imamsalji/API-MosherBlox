<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers  = DB::table('users')
            ->where('role', 'user')
            ->count();
        $totalProducts = DB::table('products')
            ->where('status', true)
            ->count();
        $dataDiagram = DB::table('orders')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, SUM(total_price) as total_penjualan")
            ->where('status', 'success')
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => [
                'user'=> $totalUsers,
                'products' => $totalProducts,
                'dataDiagram' => $dataDiagram
            ],
        ]);
    }
}
