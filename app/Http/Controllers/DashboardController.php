<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\MonitoringData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $latest = MonitoringData::latest()->first();
        $todayCount = MonitoringData::whereDate('created_at', Carbon::today())->count();
        $histories = MonitoringData::latest()->take(50)->get();

        return view('dashboard', compact('latest', 'todayCount', 'histories'));
    }

    public function json(Request $request)
    {
        $latest = MonitoringData::latest()->first();
        $today = MonitoringData::whereDate('created_at', Carbon::today());
        $todayCount = $today->count();
        $amanCount = (clone $today)->where('deteksi_burung', 'AMAN')->count();
        $terdeteksiCount = (clone $today)->where('deteksi_burung', 'TERDETEKSI')->count();

        $histories = MonitoringData::latest()->paginate(50, ['*'], 'page', $request->query('page', 1));

        $driver = DB::connection()->getDriverName();
        $hourExpr = match ($driver) {
            'sqlite' => "strftime('%H', created_at)",
            'mysql'  => "DATE_FORMAT(created_at, '%H')",
            'pgsql'  => "TO_CHAR(created_at, 'HH24')",
            default  => "strftime('%H', created_at)",
        };

        $deteksiPerJam = (clone $today)->where('deteksi_burung', 'TERDETEKSI')
            ->selectRaw("{$hourExpr} as jam, count(*) as total")
            ->groupBy('jam')
            ->orderBy('jam')
            ->pluck('total', 'jam');

        $chart = [];
        for ($i = 0; $i < 24; $i++) {
            $jam = str_pad($i, 2, '0', STR_PAD_LEFT);
            $chart[] = (int) ($deteksiPerJam[$jam] ?? 0);
        }

        return response()->json([
            'latest' => $latest,
            'todayCount' => $todayCount,
            'amanCount' => $amanCount,
            'terdeteksiCount' => $terdeteksiCount,
            'chart' => $chart,
            'histories' => $histories->items(),
            'current_page' => $histories->currentPage(),
            'last_page' => $histories->lastPage(),
            'total' => $histories->total(),
        ]);
    }

    public function clearHistory()
    {
        MonitoringData::truncate();
        Command::truncate();

        return response()->json([
            'success' => true,
            'message' => 'Semua riwayat berhasil dihapus',
            'todayCount' => 0,
            'amanCount' => 0,
            'terdeteksiCount' => 0,
            'chart' => array_fill(0, 24, 0),
            'histories' => [],
            'current_page' => 1,
            'last_page' => 1,
            'total' => 0,
            'latest' => null,
        ]);
    }
}
