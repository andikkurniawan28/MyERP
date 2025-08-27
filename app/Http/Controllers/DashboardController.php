<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\Sales;
use App\Models\SalesDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function data()
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth   = Carbon::now()->endOfMonth()->toDateString();

        $totalPurchases = self::totalPurchases($startOfMonth, $endOfMonth);
        $totalSales = self::totalSales($startOfMonth, $endOfMonth);

        $hutangUsaha = self::hutangUsaha($startOfMonth, $endOfMonth);
        $piutangUsaha = self::piutangUsaha($startOfMonth, $endOfMonth);

        $topSellingProducts = self::topSellingProducts($startOfMonth, $endOfMonth);

        $dailySales = self::dailySales($startOfMonth, $endOfMonth);

        return response()->json([
            'month'           => Carbon::now()->format('F Y'), // contoh: "Agustus 2025"
            'total_purchases' => $totalPurchases,
            'total_sales'     => $totalSales,
            'hutang_usaha'    => $hutangUsaha,
            'piutang_usaha'   => $piutangUsaha,
            'topSellingProducts'   => $topSellingProducts,
            'dailySales'   => $dailySales,
        ]);
    }

    public static function totalPurchases($startOfMonth, $endOfMonth)
    {
        return Purchase::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('grand_total');
    }

    public static function totalSales($startOfMonth, $endOfMonth)
    {
        return Sales::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('grand_total');
    }

    public static function hutangUsaha($startOfMonth, $endOfMonth)
    {
        return Purchase::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('remaining');
    }

    public static function piutangUsaha($startOfMonth, $endOfMonth)
    {
        return Sales::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('remaining');
    }

    public static function topSellingProducts($startOfMonth, $endOfMonth)
    {
        return SalesDetail::join('sales', 'sales.id', '=', 'sales_details.sales_id')
            ->join('items', 'items.id', '=', 'sales_details.item_id')
            ->whereBetween('sales.date', [$startOfMonth, $endOfMonth])
            ->select('items.id', 'items.name', DB::raw('SUM(sales_details.qty) as total_qty'))
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();
    }

    public static function dailySales($startOfMonth, $endOfMonth)
    {
        // Ambil semua sales dalam range tanggal
        $sales = Sales::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(date) as day, SUM(grand_total) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Buat array kosong untuk setiap hari di bulan ini
        $period = Carbon::parse($startOfMonth)->daysUntil(Carbon::parse($endOfMonth));
        $dailySales = [];
        foreach ($period as $date) {
            $dailySales[$date->toDateString()] = 0;
        }

        // Masukkan hasil penjualan per hari
        foreach ($sales as $s) {
            $dailySales[$s->day] = (float) $s->total;
        }

        return $dailySales; // array ['2025-08-01' => 100000, ...]
    }

}
