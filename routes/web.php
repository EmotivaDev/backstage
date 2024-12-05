<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AisController;
use App\Http\Controllers\MyprofileController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\RealtimeController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\GeofenceController;

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/statistics/fleet_position/{date}', [DashboardController::class, 'fleetPosition']);

    Route::get('ais', [AisController::class, 'indexAis'])->name('ais');
    Route::get('aishistory', [AisController::class, 'indexAisHistory'])->name('aishistory');

    // Routes for controller HistoryController
    Route::get('history', [HistoryController::class, 'index'])->name('history');
    Route::get('history/device', [HistoryController::class, 'historyDevice'])->name('history.device');
    Route::get('history/download/{file}', [HistoryController::class, 'getDownloadFile'])->name('history.download');

    // Routes for controller RealtimeController
    Route::get('realtime', [RealtimeController::class, 'index'])->name('realtime');
    Route::get('traccar/token', [RealtimeController::class, 'getTraccarToken'])->name('traccar.token');

    Route::post('trip/create', [TripController::class, 'store'])->name('trip.create');
    Route::put('trip/update/{device}', [TripController::class, 'update'])->name('trip.update');
    Route::delete('trip/{device}', [TripController::class, 'finish'])->name('trip.update');
    Route::get('trip/{device}', [TripController::class, 'show'])->name('trip.show');


    // Route::get('trails', [RealtimeController::class, 'trails'])->name('trails.select');

    // Route::post('trails', [RealtimeController::class, 'uploadTrail'])->name('trails.upload');

    // Route::post('trails/{trail}', [RealtimeController::class, 'destroyTrail'])->name('trails.delete');

     // Routes for controller GeofenceController
    Route::post('geofences/create', [GeofenceController::class, 'createGeofence'])->name('geofences.create');
    Route::get('geofences', [GeofenceController::class, 'getGeofences'])->name('geofences.show');
    Route::delete('geofences/{geofence}', [GeofenceController::class, 'destroyGeofence'])->name('geofences.delete');
    Route::put('geofences/{geofence}', [GeofenceController::class, 'updateGeofence'])->name('geofences.update');
    Route::put('geofences/area/{geofence}', [GeofenceController::class, 'updateAreaGeofence'])->name('geofences.update.area');

    // Routes for controller MyprofileController
    Route::get('myprofile', [MyprofileController::class, 'index'])->name('myprofile');
    Route::put('myprofile/{user}', [MyprofileController::class, 'update'])->name('myprofile.update');
    Route::post('myprofile/{user}', [MyprofileController::class, 'changePassword'])->name('myprofile.changePassword');

    // Routes only for controller SuperAdminController
    Route::get('super-admin', [SuperAdminController::class, 'index'])->name('super-admin');
    Route::get('companies/create', [SuperAdminController::class, 'createCompany'])->name('companies.create');
    Route::post('companies/store', [SuperAdminController::class, 'storeCompany'])->name('companies.store');
    Route::get('companies/{company}/edit', [SuperAdminController::class, 'editCompany'])->name('companies.edit');
    Route::put('companies/{company}', [SuperAdminController::class, 'updateCompany'])->name('companies.update');
    Route::get('devices/create', [SuperAdminController::class, 'createDevice'])->name('devices.create');
    Route::post('devices/store', [SuperAdminController::class, 'storeDevice'])->name('devices.store');
    Route::get('devices/{device}/edit', [SuperAdminController::class, 'editDevice'])->name('devices.edit');
    Route::put('devices/{device}', [SuperAdminController::class, 'updateDevice'])->name('devices.update');
    Route::delete('devices/{device}', [SuperAdminController::class, 'destroyDevice'])->name('devices.destroy');
});
