<?php

namespace App\Http\Controllers;

use App\Models\PlaceSubmission;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\FastApiService;
use App\Services\AI\ContentModerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PlaceSubmissionController extends Controller
{
    private function getApiToken(): ?string
    {
        $token = session('access_token');

        if (is_string($token) && trim($token) !== '') {
            return $token;
        }

        $nestedToken = data_get(session('user'), 'access_token')
            ?? data_get(session('api'), 'access_token')
            ?? data_get(session('api_user'), 'access_token');

        return is_string($nestedToken) && trim($nestedToken) !== '' ? $nestedToken : null;
    }

    private function activityLogExists(): bool
    {
        return Schema::hasTable('activity_log');
    }

    private function logActivity(
        string $module,
        $entityId = null,
        ?string $entityName = null,
        ?string $actionType = null,
        ?string $actionLabel = null,
        ?string $performedBy = null,
        ?string $performerRole = null,
        ?string $statusLabel = null,
        ?string $details = null
    ): void {
        if (!$this->activityLogExists()) return;

        DB::table('activity_log')->insert([
            'module' => $module,
            'entity_id' => $entityId,
            'entity_name' => $entityName,
            'action_type' => $actionType,
            'action_label' => $actionLabel,
            'performed_by' => $performedBy,
            'performer_role' => $performerRole,
            'status_label' => $statusLabel,
            'details' => $details,
            'created_at' => now(),
        ]);
    }

    private function getUserName()
    {
        return Auth::user()?->name ?? 'Usuario';
    }

    private function getUserRole()
    {
        return Auth::user()?->role ?? 'user';
    }

    public function store(Request $request, FastApiService $api, ContentModerator $moderator)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'price' => ['required', 'numeric', 'min:0'],
            'city' => ['required', 'string', 'max:255'],
            'city_place_id' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['required', 'string', 'min:20', 'max:1000'],
            'photos' => ['required', 'array', 'min:1', 'max:3'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'name.required' => 'Escribe el nombre del lugar.',
            'type.required' => 'Selecciona el tipo de lugar.',
            'city.required' => 'Selecciona una ciudad de la lista.',
            'price.required' => 'Indica el precio aproximado por persona.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio no puede ser negativo.',
            'rating.required' => 'Selecciona de 1 a 5 estrellas para calificar el lugar.',
            'rating.integer' => 'Selecciona una calificación válida usando las estrellas.',
            'rating.between' => 'La calificación debe estar entre 1 y 5 estrellas.',
            'lat.required' => 'Selecciona la ubicación exacta en el mapa.',
            'lng.required' => 'Selecciona la ubicación exacta en el mapa.',
            'lat.between' => 'La latitud seleccionada no es válida.',
            'lng.between' => 'La longitud seleccionada no es válida.',
            'photos.required' => 'Agrega al menos una foto del lugar.',
            'photos.max' => 'Puedes agregar como máximo 3 fotos.',
            'photos.*.image' => 'Cada archivo debe ser una imagen válida.',
            'photos.*.mimes' => 'Las fotos deben ser JPG, PNG o WEBP.',
            'photos.*.max' => 'Cada foto debe pesar como máximo 5 MB.',
            'description.max' => 'La descripción no puede superar 1000 caracteres.',
            'description.required' => 'Describe el lugar antes de enviar la solicitud.',
            'description.min' => 'La descripción debe tener al menos 20 caracteres.',
        ]);

        $moderation = $moderator->review($validated['description'], 'descripción de lugar');
        if (!$moderation['allowed']) {
            return back()->withInput()->withErrors([
                'description' => 'La descripción debe cambiarse porque infringe las normas: '.($moderation['reason'] ?? 'contenido no permitido.'),
            ]);
        }

        $token = $this->getApiToken();
        if (!$token) {
            return redirect()->route('login')
                ->with('error', 'Tu sesión venció. Inicia sesión nuevamente para enviar el lugar a revisión.');
        }

        $apiPayload = [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'rating' => $validated['rating'] ?? 0,
            'price' => $validated['price'],
            'city' => $validated['city'],
            'city_place_id' => $validated['city_place_id'] ?? '',
            'address' => $validated['address'] ?? '',
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'description' => $validated['description'] ?? '',
        ];

        try {
            $apiResponse = $api->postMultipart(
                '/approvals',
                $apiPayload,
                $request->file('photos', []),
                $token,
                'photos'
            );
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()
                ->with('error', 'No pudimos conectar con el servicio de revisión. Tus datos siguen en el formulario; inténtalo nuevamente.');
        }

        if (!$apiResponse->successful()) {
            if ($apiResponse->status() === 401) {
                session()->forget(['access_token', 'api', 'api_user']);

                return redirect()->route('login')
                    ->with('error', 'Tu sesión venció. Inicia sesión nuevamente para continuar.');
            }

            $detail = $apiResponse->json('detail');
            $message = is_string($detail) && trim($detail) !== ''
                ? $detail
                : 'No pudimos enviar el lugar a revisión. Revisa los datos e inténtalo nuevamente.';

            return back()->withInput()->with('error', $message);
        }

        $submission = DB::transaction(function () use ($validated, $request, $apiResponse) {

            $submission = PlaceSubmission::create([
                'user_id' => Auth::id(),
                'platform_submission_id' => $apiResponse->json('approval_id'),
                'name' => $validated['name'],
                'type' => $validated['type'],
                'rating' => $validated['rating'] ?? 0,
                'price' => $validated['price'],
                'city' => $validated['city'],
                'city_place_id' => $validated['city_place_id'] ?? null,
                'address' => $validated['address'] ?? null,
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'description' => $validated['description'] ?? null,
                'status' => 'pending',
                'sent_to_flask' => true,
                'sent_to_flask_at' => now(),
            ]);

            foreach ($request->file('photos', []) as $photo) {
                $path = $photo->store('place-submissions', 'public');

                $submission->photos()->create([
                    'path' => $path,
                ]);
            }

            $this->logActivity(
                module: 'approvals',
                entityId: $submission->id,
                entityName: $submission->name,
                actionType: 'created',
                actionLabel: 'Solicitud enviada',
                performedBy: $this->getUserName(),
                performerRole: $this->getUserRole(),
                statusLabel: 'Pendiente',
                details: "Se envió solicitud de aprobación para '{$submission->name}' en {$submission->city}"
            );

            return $submission;
        });

        UserNotification::sendTo(
            user: Auth::id(),
            type: 'approval_sent',
            title: 'Aprobación enviada',
            body: "Tu solicitud para '{$submission->name}' fue enviada a revisión.",
            url: route('place-submissions.show', $submission),
            actor: Auth::user(),
            data: ['place_submission_id' => $submission->id]
        );

        return redirect()
            ->to(route('places.mine').'#approvals')
            ->with('success', '¡Listo! El lugar fue enviado al administrador y está pendiente de revisión.');
    }

    public function index()
    {
        $submissions = PlaceSubmission::with('photos')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('place-submissions.index', compact('submissions'));
    }

    public function show(PlaceSubmission $placeSubmission)
    {
        abort_unless($placeSubmission->user_id === Auth::id(), 403);

        $placeSubmission->load('photos');

        return view('place-submissions.show', compact('placeSubmission'));
    }

    public function destroy(PlaceSubmission $placeSubmission)
    {
        abort_unless($placeSubmission->user_id === Auth::id(), 403);

        $placeSubmission->load('photos');

        foreach ($placeSubmission->photos as $photo) {
            if ($photo->path && Storage::disk('public')->exists($photo->path)) {
                Storage::disk('public')->delete($photo->path);
            }
        }

        $this->logActivity(
            module: 'approvals',
            entityId: $placeSubmission->id,
            entityName: $placeSubmission->name,
            actionType: 'deleted',
            actionLabel: 'Solicitud eliminada',
            performedBy: $this->getUserName(),
            performerRole: $this->getUserRole(),
            statusLabel: 'Cancelado',
            details: "Se eliminó la solicitud '{$placeSubmission->name}'"
        );

        UserNotification::sendTo(
            user: Auth::id(),
            type: 'place_deleted',
            title: 'Lugar eliminado',
            body: "Eliminaste la solicitud '{$placeSubmission->name}'.",
            url: route('places.mine').'#approvals',
            actor: Auth::user(),
            data: ['place_submission_id' => $placeSubmission->id]
        );

        $placeSubmission->delete();

        return redirect()
            ->to(route('places.mine').'#approvals')
            ->with('success', 'Solicitud eliminada correctamente.');
    }

    public function approve(PlaceSubmission $placeSubmission)
    {
        $placeSubmission->update([
            'status' => 'approved',
        ]);

        UserNotification::sendTo(
            user: $placeSubmission->user_id,
            type: 'approval_accepted',
            title: 'Aprobación aceptada',
            body: "Tu lugar '{$placeSubmission->name}' fue aprobado.",
            url: route('place-submissions.show', $placeSubmission),
            actor: Auth::user(),
            data: ['place_submission_id' => $placeSubmission->id]
        );

        return back()->with('success', 'Solicitud aprobada correctamente.');
    }

    public function reject(PlaceSubmission $placeSubmission)
    {
        $placeSubmission->update([
            'status' => 'rejected',
        ]);

        UserNotification::sendTo(
            user: $placeSubmission->user_id,
            type: 'approval_rejected',
            title: 'Aprobación rechazada',
            body: "Tu lugar '{$placeSubmission->name}' necesita ajustes antes de publicarse.",
            url: route('place-submissions.show', $placeSubmission),
            actor: Auth::user(),
            data: ['place_submission_id' => $placeSubmission->id]
        );

        return back()->with('success', 'Solicitud rechazada correctamente.');
    }
}
