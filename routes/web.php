<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TargetController;
use App\Http\Controllers\AstroController;
use App\Http\Livewire\Select2Dropdown;

use Japp\Astrolib\Astrolib;

use Carbon\Carbon;
use Carbon\CarbonImmutable;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/chart', [AstroController::class, 'chart'])->name('chart');
Route::get('/observability', [AstroController::class, 'observability']);
Route::get('/exportpdf', [AstroController::class, 'exportPDF']);

Route::get('/', Select2Dropdown::class);


Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


Route::resource('targets', TargetController::class);


/* -------------------- Tests -------------------- */ 

// For sesame services testing - REMOVE!!
Route::get('/sesame/{name}', function($name) {
    $astrolib = new Astrolib();
    return $astrolib->sesame($name);
});

// For sesame services testing - REMOVE!!
Route::get('/suninfo', function() {
    return sun_info();
});

Route::get('/test', function() {
    $now = Carbon::now();

    $sun_info_date = date_sun_info($now->timestamp, 28.291668, -16.496668);
    $twilight ="astronomical";
    $twilight_end = Carbon::parse($sun_info_date[$twilight."_twilight_end"]);

    $sun_info_next_day = date_sun_info($now->add(1, 'day')->timestamp, 28.291668, -16.496668);
    $twilight_begin = Carbon::parse($sun_info_next_day[$twilight."_twilight_begin"]);

    return json_encode(['twilight_end' => $twilight_end, 'twilight_begin' => $twilight_begin]);
});