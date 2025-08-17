<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // On SQLite (local dev), keep using decimal lat/long only.
            return;
        }

        Schema::table('courts', function (Blueprint $table) use ($driver) {
            if (!Schema::hasColumn('courts', 'location_point')) {
                $table->point('location_point')->nullable()->after('longitude');
                if ($driver === 'mysql') {
                    $table->spatialIndex('location_point');
                } else {
                    $table->index('location_point');
                }
            }
        });

        Schema::table('partner_requests', function (Blueprint $table) use ($driver) {
            if (!Schema::hasColumn('partner_requests', 'location_point')) {
                $table->point('location_point')->nullable()->after('longitude');
                if ($driver === 'mysql') {
                    $table->spatialIndex('location_point');
                } else {
                    $table->index('location_point');
                }
            }
        });
    }

    public function down(): void
    {
        // Dropping the column will drop related indexes automatically on most DBs
        if (Schema::hasColumn('courts', 'location_point')) {
            Schema::table('courts', function (Blueprint $table): void {
                $table->dropColumn('location_point');
            });
        }

        if (Schema::hasColumn('partner_requests', 'location_point')) {
            Schema::table('partner_requests', function (Blueprint $table): void {
                $table->dropColumn('location_point');
            });
        }
    }
};

