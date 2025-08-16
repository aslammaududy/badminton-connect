<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');
Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

// App pages
Route::get('/courts', \App\Livewire\Courts\Index::class)->name('courts.index');
Route::get('/bookings/create', \App\Livewire\Bookings\Create::class)->middleware('auth')->name('bookings.create');
Route::get('/tournaments', \App\Livewire\Tournaments\Index::class)->name('tournaments.index');
Route::get('/partners/find', \App\Livewire\Partners\Find::class)->name('partners.find');

require __DIR__.'/auth.php';
