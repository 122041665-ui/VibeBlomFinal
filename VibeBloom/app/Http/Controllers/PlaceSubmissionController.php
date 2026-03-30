<?php

namespace App\Http\Controllers;

use App\Models\PlaceSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PlaceSubmissionController extends Controller
{
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'rating' => ['nullable', 'integer', 'min:0', 'max:5'],
            'price' => ['required', 'numeric', 'min:0'],
            'city' => ['required', 'string', 'max:255'],
            'city_place_id' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'description' => ['nullable', 'string'],
            'photos' => ['required', 'array', 'min:1', 'max:3'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        DB::transaction(function () use ($validated, $request) {

            $submission = PlaceSubmission::create([
                'user_id' => Auth::id(),
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
        });

        return redirect()
            ->route('place-submissions.index')
            ->with('success', 'Aprobación enviada correctamente.');
    }

    public function index()
    {
        $submissions = PlaceSubmission::with('photos')
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
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

        $placeSubmission->delete();

        return redirect()
            ->route('place-submissions.index')
            ->with('success', 'Solicitud eliminada correctamente.');
    }
}