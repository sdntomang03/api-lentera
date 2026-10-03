<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fcm_devices', function (Blueprint $table): void {
            $table->string('username')->nullable()->after('user_id');
        });

        DB::table('fcm_devices')
            ->select(['id', 'user_id'])
            ->orderBy('id')
            ->chunkById(100, function ($devices): void {
                $usernames = DB::table('users')
                    ->whereIn('id', $devices->pluck('user_id'))
                    ->pluck('username', 'id');

                foreach ($devices as $device) {
                    DB::table('fcm_devices')
                        ->where('id', $device->id)
                        ->update(['username' => $usernames[$device->user_id] ?? null]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('fcm_devices', function (Blueprint $table): void {
            $table->dropColumn('username');
        });
    }
};
