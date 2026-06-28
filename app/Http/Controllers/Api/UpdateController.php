<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonitoringData;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'status_alat' => 'required|string',
            'deteksi_burung' => 'required|string',
            'status_buzzer' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        MonitoringData::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data tersimpan',
        ], 201);
    }
}
