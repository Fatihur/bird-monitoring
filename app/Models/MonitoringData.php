<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringData extends Model
{
    protected $fillable = [
        'status_alat',
        'deteksi_burung',
        'status_buzzer',
        'status_relay',
        'status_pir',
        'keterangan',
    ];
}
