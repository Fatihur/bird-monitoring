<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\MonitoringData;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $latest = MonitoringData::latest()->first();
        $todayCount = MonitoringData::whereDate('created_at', Carbon::today())->count();
        $histories = MonitoringData::latest()->take(50)->get();

        return view('dashboard', compact('latest', 'todayCount', 'histories'));
    }

    public function json()
    {
        $latest = MonitoringData::latest()->first();
        $today = MonitoringData::whereDate('created_at', Carbon::today());
        $todayCount = $today->count();
        $amanCount = (clone $today)->where('deteksi_burung', 'AMAN')->count();
        $terdeteksiCount = (clone $today)->where('deteksi_burung', 'TERDETEKSI')->count();
        $histories = MonitoringData::latest()->take(50)->get();

        return response()->json([
            'latest' => $latest,
            'todayCount' => $todayCount,
            'amanCount' => $amanCount,
            'terdeteksiCount' => $terdeteksiCount,
            'histories' => $histories,
        ]);
    }

    public function clearHistory()
    {
        MonitoringData::truncate();
        Command::truncate();

        return response()->json(['success' => true]);
    }
}
