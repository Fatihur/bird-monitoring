<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->string('pir')->nullable()->after('all_off')->comment('ON or OFF');
        });

        Schema::table('monitoring_data', function (Blueprint $table) {
            $table->string('status_pir')->after('status_relay')->default('AKTIF');
        });
    }

    public function down(): void
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->dropColumn('pir');
        });

        Schema::table('monitoring_data', function (Blueprint $table) {
            $table->dropColumn('status_pir');
        });
    }
};
