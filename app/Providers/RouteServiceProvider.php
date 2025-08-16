<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');
            return [
                Limit::perMinute(5)->by($email.'|'.$request->ip())->response(function () {
                    return response()->json(['message' => 'Too many login attempts. Try again later.'], 429);
                }),
            ];
        });

        RateLimiter::for('register', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip())->response(function () {
                    return response()->json(['message' => 'Too many registration attempts. Try again later.'], 429);
                }),
            ];
        });
    }
}

