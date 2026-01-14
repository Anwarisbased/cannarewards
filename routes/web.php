<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Redirect to admin panel for central domain
    return redirect()->route('filament.admin.pages.dashboard');
});

Route::get('/central-test', function () {
    return 'I am Central';
});
