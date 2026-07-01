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
            ->where(function ($q) {
                $q->whereNotNull('buzzer')
                  ->orWhereNotNull('relay')
                  ->orWhereNotNull('all_off')
                  ->orWhereNotNull('pir');
            })
            ->latest()
            ->first();

        return response()->json([
            'buzzer' => $command?->buzzer ?? null,
            'relay' => $command?->relay ?? null,
            'pir' => $command?->pir ?? null,
            'all_off' => $command?->all_off ?? null,
            'command_id' => $command?->id ?? null,
        ]);
    }

    public function toggleRelay()
    {
        $latest = MonitoringData::latest()->first();
        $currentRelay = $latest?->status_relay ?? 'OFF';
        $newState = $currentRelay === 'ON' ? 'OFF' : 'ON';

        $command = Command::create(['relay' => $newState]);

        MonitoringData::create([
            'status_alat' => 'AKTIF',
            'deteksi_burung' => 'AMAN',
            'status_buzzer' => $latest?->status_buzzer ?? 'OFF',
            'status_relay' => $newState,
            'status_pir' => $latest?->status_pir ?? 'AKTIF',
            'keterangan' => "Relay {$newState} dari dashboard",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Relay {$newState}",
            'relay' => $newState,
            'command_id' => $command->id,
        ]);
    }

    public function triggerBuzzer()
    {
        $latest = MonitoringData::latest()->first();

        $command = Command::create(['buzzer' => 'TRIGGER']);

        MonitoringData::create([
            'status_alat' => 'AKTIF',
            'deteksi_burung' => 'AMAN',
            'status_buzzer' => 'ON',
            'status_relay' => $latest?->status_relay ?? 'OFF',
            'status_pir' => $latest?->status_pir ?? 'AKTIF',
            'keterangan' => 'Buzzer dipicu dari dashboard',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Buzzer dipicu (5 detik)',
            'buzzer' => 'TRIGGER',
            'command_id' => $command->id,
        ]);
    }

    public function togglePir()
    {
        $latest = MonitoringData::latest()->first();
        $currentPir = $latest?->status_pir ?? 'AKTIF';
        $newState = $currentPir === 'AKTIF' ? 'NONAKTIF' : 'AKTIF';

        $command = Command::create(['pir' => $newState]);

        MonitoringData::create([
            'status_alat' => 'AKTIF',
            'deteksi_burung' => 'AMAN',
            'status_buzzer' => $latest?->status_buzzer ?? 'OFF',
            'status_relay' => $latest?->status_relay ?? 'OFF',
            'status_pir' => $newState,
            'keterangan' => "Sensor PIR {$newState} dari dashboard",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Sensor PIR {$newState}",
            'pir' => $newState,
            'command_id' => $command->id,
        ]);
    }

    public function allOff()
    {
        $command = Command::create(['all_off' => '1']);

        MonitoringData::create([
            'status_alat' => 'AKTIF',
            'deteksi_burung' => 'AMAN',
            'status_buzzer' => 'OFF',
            'status_relay' => 'OFF',
            'status_pir' => 'NONAKTIF',
            'keterangan' => 'Semua perangkat dimatikan dari dashboard',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Semua perangkat dimatikan',
            'relay' => 'OFF',
            'buzzer' => 'OFF',
            'pir' => 'NONAKTIF',
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
            'relay' => null,
            'all_off' => null,
            'pir' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Command acknowledged',
        ]);
    }
}
