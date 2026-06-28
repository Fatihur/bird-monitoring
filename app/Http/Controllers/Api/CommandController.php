<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Command;
use App\Models\MonitoringData;
use Illuminate\Http\Request;

class CommandController extends Controller
{
    public function index()
    {
        $command = Command::where('acknowledged', false)
            ->whereNotNull('buzzer')
            ->latest()
            ->first();

        return response()->json([
            'buzzer' => $command?->buzzer ?? null,
            'command_id' => $command?->id ?? null,
        ]);
    }

    public function buzzerOn()
    {
        $command = Command::create(['buzzer' => 'ON']);

        MonitoringData::create([
            'status_alat' => 'AKTIF',
            'deteksi_burung' => 'AMAN',
            'status_buzzer' => 'ON',
            'keterangan' => 'Buzzer dinyalakan manual dari dashboard',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Buzzer ON',
            'command_id' => $command->id,
        ]);
    }

    public function buzzerOff()
    {
        $command = Command::create(['buzzer' => 'OFF']);

        MonitoringData::create([
            'status_alat' => 'AKTIF',
            'deteksi_burung' => 'AMAN',
            'status_buzzer' => 'OFF',
            'keterangan' => 'Buzzer dimatikan manual dari dashboard',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Buzzer OFF',
            'command_id' => $command->id,
        ]);
    }

    public function ack(Request $request)
    {
        $validated = $request->validate([
            'command_id' => 'required|integer|exists:commands,id',
        ]);

        $command = Command::find($validated['command_id']);
        $command->update([
            'acknowledged' => true,
            'buzzer' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Command acknowledged',
        ]);
    }
}
