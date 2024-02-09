<?php

use App\Http\Controllers\ScanAgendaInvitationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->middleware('guest');

Route::get('/home', function () {
    $user = auth()?->user();

    if ($user?->hasRole(\App\Enums\UserRole::ADMIN)) {
        return redirect()->route('filament.admin.auth.login');
    }

    if ($user?->hasRole(\App\Enums\UserRole::STAFF)) {
        return redirect()->route('filament.staff.auth.login');
    }

    auth()->logout();

    return redirect('/');
})->middleware('auth');

Route::get('/login', function () {
    return redirect('/');
})->name('login');

Route::get('/scan/{agendaId}/{code}', ScanAgendaInvitationController::class)->name('scan-agenda-invitation');

Route::get('filesystem-check', function () {
    return response()->json([
        'links' => config('filesystems.links'),
        'public_path' => [
            'value' => config('filesystems.public_path'),
            'base_path' => base_path(config('filesystems.public_path')),
            'realpath' => realpath(base_path(config('filesystems.public_path'))),
        ],
        'storage_path' => [
            'value' => config('filesystems.storage_path'),
            'base_path' => base_path(config('filesystems.storage_path')),
            'realpath' => realpath(base_path(config('filesystems.storage_path'))),
        ],
        'manual' => [
            public_path('storage') => storage_path('app/public'),
        ],
    ]);
});
