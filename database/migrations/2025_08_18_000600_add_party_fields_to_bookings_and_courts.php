<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'desired_size')) {
                $table->unsignedTinyInteger('desired_size')->default(8)->after('status');
            }
            if (!Schema::hasColumn('bookings', 'open_to_join')) {
                // Default false; will be set true explicitly from map flow
                $table->boolean('open_to_join')->default(false)->after('desired_size');
            }
        });

        Schema::table('courts', function (Blueprint $table) {
            if (!Schema::hasColumn('courts', 'total_courts')) {
                $table->unsignedSmallInteger('total_courts')->default(1)->after('hourly_rate');
            }
            if (!Schema::hasColumn('courts', 'owner_user_id')) {
                $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete()->after('total_courts');
            }
            // Ensure place_id is indexed for quick lookups; uniqueness enforces one venue per Place
            if (Schema::hasColumn('courts', 'place_id')) {
                // Guard against duplicate index creation in different environments
                try { $table->unique('place_id', 'courts_place_id_unique'); } catch (Throwable $e) { /* ignore */ }
            }
        });

        if (!Schema::hasTable('booking_participants')) {
            Schema::create('booking_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('status')->default('requested'); // requested|accepted|declined
                $table->timestamp('accepted_at')->nullable();
                $table->timestamps();
                $table->unique(['booking_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('booking_participants')) {
            Schema::dropIfExists('booking_participants');
        }

        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'open_to_join')) {
                $table->dropColumn('open_to_join');
            }
            if (Schema::hasColumn('bookings', 'desired_size')) {
                $table->dropColumn('desired_size');
            }
        });

        Schema::table('courts', function (Blueprint $table) {
            if (Schema::hasColumn('courts', 'owner_user_id')) {
                $table->dropConstrainedForeignId('owner_user_id');
            }
            if (Schema::hasColumn('courts', 'total_courts')) {
                $table->dropColumn('total_courts');
            }
            // Do not drop unique index on place_id in down() to avoid data loss risk; safe to keep
        });
    }
};
