<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Court;
use App\Models\GameMatch;
use App\Models\PartnerRequest;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();

        $courts = Court::factory(5)->create();

        // Bookings
        Booking::factory(15)->create();

        // Tournaments
        $tournaments = Tournament::factory(3)->create();
        foreach ($tournaments as $tournament) {
            $tournament->participants()->attach($users->random(6)->pluck('id'));
        }

        // Matches
        GameMatch::factory(10)->create();

        // Partner Requests
        PartnerRequest::factory(8)->create();
    }
}
