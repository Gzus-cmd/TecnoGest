<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/devices/{type}/{id}/full-report', [DeviceReportController::class, 'fullReport'])
    ->name('devices.full-report')
    ->middleware('auth');
