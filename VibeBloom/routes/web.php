<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AIVoiceController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MemoryController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PlacesMapController;
use App\Http\Controllers\PlaceSubmissionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewReplyController;
use App\Http\Controllers\RouteVoiceController;

/*
|--------------------------------------------------------------------------
| HOME (PÚBLICO)
|--------------------------------------------------------------------------
*/
Route::get('/', [PlaceController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| PLACES (PÚBLICO)
|--------------------------------------------------------------------------
*/
Route::get('/places', [PlaceController::class, 'index'])->name('places.index');

/*
|--------------------------------------------------------------------------
| PRUEBA DE CORREO (solo local)
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
    Route::get('/correo-test', function () {
        Mail::raw('Prueba Brevo', function ($msg) {
            $msg->to('tu-correo@gmail.com')->subject('Test Brevo');
        });

        return 'Correo enviado';
    });
}

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS SOLO CON LOGIN
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | FAVORITOS
    |--------------------------------------------------------------------------
    */
    Route::post('/favorite/{place}', [FavoriteController::class, 'toggle'])->name('favorite.toggle');
    Route::get('/mis-favoritos', [FavoriteController::class, 'mine'])->name('favorites.mine');

    /*
    |--------------------------------------------------------------------------
    | MEMORIES
    |--------------------------------------------------------------------------
    */
    Route::get('/memories', [MemoryController::class, 'index'])->name('memories.index');
    Route::get('/memories/create', [MemoryController::class, 'create'])->name('memories.create');
    Route::post('/memories', [MemoryController::class, 'store'])->name('memories.store');
    Route::get('/memories/{memory}/edit', [MemoryController::class, 'edit'])->name('memories.edit');
    Route::put('/memories/{memory}', [MemoryController::class, 'update'])->name('memories.update');
    Route::delete('/memories/{memory}', [MemoryController::class, 'destroy'])->name('memories.destroy');
});

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS CON LOGIN + VERIFIED
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [PlaceController::class, 'dashboard'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | MAPA DE LUGARES
    |--------------------------------------------------------------------------
    */
    Route::get('/places/map', [PlacesMapController::class, 'map'])->name('places.map');
    Route::get('/places/geojson', [PlacesMapController::class, 'geojson'])->name('places.geojson');

    /*
    |--------------------------------------------------------------------------
    | MIS LUGARES
    |--------------------------------------------------------------------------
    */
    Route::get('/mis-lugares', [PlaceController::class, 'myPlaces'])->name('places.mine');

    /*
    |--------------------------------------------------------------------------
    | CREAR / EDITAR / VER LUGARES REALES
    |--------------------------------------------------------------------------
    */
    Route::get('/places/create', [PlaceController::class, 'create'])->name('places.create');
    Route::post('/places', [PlaceController::class, 'store'])->name('places.store');
    Route::get('/places/{place}', [PlaceController::class, 'show'])->name('places.show');
    Route::get('/places/{place}/edit', [PlaceController::class, 'edit'])->name('places.edit');
    Route::put('/places/{place}', [PlaceController::class, 'update'])->name('places.update');
    Route::delete('/places/{place}', [PlaceController::class, 'destroy'])->name('places.destroy');

    /*
    |--------------------------------------------------------------------------
    | APROBACIONES DE LUGARES DEL USUARIO
    |--------------------------------------------------------------------------
    */
    Route::post('/place-submissions', [PlaceSubmissionController::class, 'store'])
        ->name('place-submissions.store');

    Route::get('/mis-aprobaciones', [PlaceSubmissionController::class, 'index'])
        ->name('place-submissions.index');

    Route::get('/mis-aprobaciones/{placeSubmission}', [PlaceSubmissionController::class, 'show'])
        ->name('place-submissions.show');

    Route::delete('/mis-aprobaciones/{placeSubmission}', [PlaceSubmissionController::class, 'destroy'])
        ->name('place-submissions.destroy');

    /*
    |--------------------------------------------------------------------------
    | REVIEWS
    |--------------------------------------------------------------------------
    */
    Route::post('/places/{place}/reviews', [ReviewController::class, 'store'])->name('places.reviews.store');

    Route::delete('/places/{place}/reviews/{review}', [ReviewController::class, 'destroy'])
        ->name('places.reviews.destroy');

    Route::post('/places/{place}/reviews/{review}/replies', [ReviewReplyController::class, 'store'])
        ->name('places.reviews.replies.store');

    Route::delete('/places/{place}/reviews/{review}/replies/{reply}', [ReviewReplyController::class, 'destroy'])
        ->name('places.reviews.replies.destroy');

    /*
    |--------------------------------------------------------------------------
    | AI VOICE
    |--------------------------------------------------------------------------
    */
    Route::get('/ai/voice', [AIVoiceController::class, 'index'])->name('ai.voice.index');
    Route::get('/ai/voz', [AIVoiceController::class, 'index'])->name('ai.voz.index');
    Route::post('/ai/voz/recomendar', [AIVoiceController::class, 'recommendFromAudio'])->name('ai.voz.recomendar');

    /*
    |--------------------------------------------------------------------------
    | RUTA POR VOZ (OPENAI TTS)
    |--------------------------------------------------------------------------
    */
    Route::post('/route-voice', [RouteVoiceController::class, 'generate'])
        ->name('route.voice');

    /*
    |--------------------------------------------------------------------------
    | PANEL ADMINISTRATIVO OPERATIVO
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin|moderator'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/places', [AdminController::class, 'places'])->name('places');

        Route::post('/place-submissions/{placeSubmission}/approve', [PlaceSubmissionController::class, 'approve'])
            ->name('place-submissions.approve');

        Route::post('/place-submissions/{placeSubmission}/reject', [PlaceSubmissionController::class, 'reject'])
            ->name('place-submissions.reject');
    });

    /*
    |--------------------------------------------------------------------------
    | CONTROL TOTAL DE JERARQUÍA
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::post('/user/{user}/make-admin', [AdminController::class, 'makeAdmin'])->name('makeAdmin');
        Route::post('/user/{user}/remove-admin', [AdminController::class, 'removeAdmin'])->name('removeAdmin');
    });
});