<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\MonitoringData;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $deteksiPerJam = (clone $today)->where('deteksi_burung', 'TERDETEKSI')
            ->selectRaw("strftime('%H', created_at) as jam, count(*) as total")
            ->groupBy('jam')
            ->orderBy('jam')
            ->pluck('total', 'jam');

        $chart = [];
        for ($i = 0; $i < 24; $i++) {
            $jam = str_pad($i, 2, '0', STR_PAD_LEFT);
            $chart[] = $deteksiPerJam[$jam] ?? 0;
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

        return response()->json(['success' => true]);
    }
}
