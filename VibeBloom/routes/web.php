<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AIVoiceController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MemoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PlacesMapController;
use App\Http\Controllers\PlaceSubmissionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewReplyController;
use App\Http\Controllers\RouteVoiceController;
use App\Http\Controllers\UserNetworkController;

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

$legalPages = [
    'about' => [
        'title' => 'Acerca de VibeBloom',
        'sections' => [
            ['title' => 'Nuestra plataforma', 'body' => 'VibeBloom es una comunidad para descubrir lugares, compartir experiencias y organizar sitios favoritos. Los lugares enviados por usuarios pasan por revisión antes de mostrarse públicamente.'],
            ['title' => 'Contenido de la comunidad', 'body' => 'Las fotografías, descripciones, reseñas y respuestas pertenecen a quienes las publican. Usamos moderación automática y revisión administrativa para mantener una experiencia útil y respetuosa.'],
        ],
    ],
    'service-conditions' => [
        'title' => 'Condiciones del servicio',
        'sections' => [
            ['title' => 'Uso responsable', 'body' => 'Debes proporcionar información veraz, respetar a otros usuarios y abstenerte de publicar contenido ilegal, ofensivo, discriminatorio, engañoso o que vulnere derechos de terceros.'],
            ['title' => 'Disponibilidad', 'body' => 'Procuramos mantener el servicio disponible y actualizado, pero las rutas, precios, horarios y datos de establecimientos pueden cambiar. Verifica información sensible directamente con el lugar.'],
            ['title' => 'Moderación y cuenta', 'body' => 'VibeBloom puede rechazar, ocultar o retirar contenido que incumpla estas condiciones y restringir cuentas ante usos abusivos o riesgos para la comunidad.'],
        ],
    ],
    'data-privacy' => [
        'title' => 'Privacidad y datos',
        'sections' => [
            ['title' => 'Datos que tratamos', 'body' => 'Tratamos datos de registro, foto de perfil, contenido publicado, favoritos e interacciones necesarias para operar la cuenta. La ubicación se utiliza en el navegador para mapas e indicaciones cuando otorgas permiso.'],
            ['title' => 'Finalidad y conservación', 'body' => 'Usamos los datos para autenticarte, mostrar tus publicaciones, personalizar funciones, prevenir abuso y mejorar la plataforma. Los conservamos mientras la cuenta esté activa o exista una obligación legítima.'],
            ['title' => 'Tus decisiones', 'body' => 'Puedes actualizar tu perfil, retirar contenido propio y eliminar tu cuenta desde la configuración. No vendas datos sensibles ni publiques información personal de terceros sin autorización.'],
        ],
    ],
];

foreach ($legalPages as $slug => $page) {
    Route::get('/'.$slug, fn () => view('legal', $page))->name('legal.'.$slug);
}

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
    | NOTIFICACIONES
    |--------------------------------------------------------------------------
    */
    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notificaciones/{notification}/abrir', [NotificationController::class, 'open'])->name('notifications.open');
    Route::post('/notificaciones/marcar-leidas', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

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
    | COMUNIDAD / RED SOCIAL
    |--------------------------------------------------------------------------
    */
    Route::get('/usuarios', [UserNetworkController::class, 'index'])->name('users.index');
    Route::get('/usuarios/{user}', [UserNetworkController::class, 'show'])->name('users.show');
    Route::post('/usuarios/{user}/seguir', [UserNetworkController::class, 'follow'])->name('users.follow');
    Route::delete('/usuarios/{user}/seguir', [UserNetworkController::class, 'unfollow'])->name('users.unfollow');

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
