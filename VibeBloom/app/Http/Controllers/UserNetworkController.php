<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\PlaceSubmission;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\FastApiService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserNetworkController extends Controller
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

    private function normalizeList($json): Collection
    {
        if (is_array($json) && isset($json['data']) && is_array($json['data'])) {
            return collect($json['data']);
        }

        if (is_array($json) && isset($json['items']) && is_array($json['items'])) {
            return collect($json['items']);
        }

        return is_array($json) ? collect($json) : collect();
    }

    private function fetchApiPlaces(FastApiService $api): Collection
    {
        try {
            $response = $api->get('/places');

            if (!$response->successful()) {
                return collect();
            }

            return $this->normalizeList($response->json())->filter(fn ($place) => is_array($place))->values();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function fetchApiUsers(FastApiService $api): Collection
    {
        $token = $this->getApiToken();

        if (!$token) {
            return collect();
        }

        try {
            $response = $api->get('/users', $token);

            if (!$response->successful()) {
                return collect();
            }

            return $this->normalizeList($response->json())->filter(fn ($user) => is_array($user))->values();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function extractUsersFromPlaces(Collection $places): Collection
    {
        return $places
            ->flatMap(function (array $place) {
                $users = [];

                if (isset($place['user']) && is_array($place['user'])) {
                    $users[] = $place['user'];
                }

                foreach (($place['reviews'] ?? []) as $review) {
                    if (isset($review['user']) && is_array($review['user'])) {
                        $users[] = $review['user'];
                    }

                    foreach (($review['replies'] ?? []) as $reply) {
                        if (isset($reply['user']) && is_array($reply['user'])) {
                            $users[] = $reply['user'];
                        }
                    }
                }

                return $users;
            })
            ->filter(fn ($user) => is_array($user) && !empty($user['email']))
            ->unique(fn ($user) => mb_strtolower((string) $user['email']))
            ->values();
    }

    private function photoPathFromApi(?string $photoUrl): ?string
    {
        $photoUrl = trim((string) $photoUrl);

        if ($photoUrl === '') {
            return null;
        }

        if (str_starts_with($photoUrl, '/storage/')) {
            return ltrim(substr($photoUrl, strlen('/storage/')), '/');
        }

        if (str_starts_with($photoUrl, 'storage/')) {
            return ltrim(substr($photoUrl, strlen('storage/')), '/');
        }

        return null;
    }

    private function syncPlatformUsers(FastApiService $api, ?Collection $apiPlaces = null): Collection
    {
        $apiPlaces = $apiPlaces ?: $this->fetchApiPlaces($api);

        $users = $this->fetchApiUsers($api)
            ->merge($this->extractUsersFromPlaces($apiPlaces))
            ->filter(fn ($user) => is_array($user) && !empty($user['email']))
            ->unique(fn ($user) => mb_strtolower((string) $user['email']))
            ->values();

        $users->each(function (array $apiUser) {
            $email = trim((string) ($apiUser['email'] ?? ''));
            $name = trim((string) ($apiUser['name'] ?? ''));

            if ($email === '') {
                return;
            }

            $user = User::firstOrNew(['email' => $email]);
            $user->name = $name !== '' ? $name : Str::before($email, '@');
            $user->platform_user_id = $apiUser['id'] ?? $user->platform_user_id;

            $profilePhotoUrl = trim((string) ($apiUser['profile_photo_url'] ?? ''));
            $photoPath = $this->photoPathFromApi($profilePhotoUrl);
            if ($photoPath && !$user->profile_photo_path) {
                $user->profile_photo_path = $photoPath;
            }

            if ($profilePhotoUrl !== '') {
                $user->external_profile_photo_url = $profilePhotoUrl;
            }

            if (!$user->exists || !$user->password) {
                $user->password = Hash::make(Str::random(32));
            }

            $user->save();
        });

        return $apiPlaces;
    }

    private function platformPlaceCounts(Collection $apiPlaces): array
    {
        return $apiPlaces
            ->filter(fn ($place) => !empty($place['user']['email']))
            ->groupBy(fn ($place) => mb_strtolower((string) $place['user']['email']))
            ->map(fn ($items) => $items->count())
            ->all();
    }

    private function apiPhotoUrl(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return asset('images/vibebloom.png');
        }

        if (
            str_starts_with($path, 'http://') ||
            str_starts_with($path, 'https://') ||
            str_starts_with($path, '//') ||
            str_starts_with($path, 'data:')
        ) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            $relativePath = ltrim(substr($path, strlen('/storage/')), '/');
            return Storage::disk('public')->exists($relativePath)
                ? asset(ltrim($path, '/'))
                : asset('images/vibebloom.png');
        }

        if (str_starts_with($path, 'storage/')) {
            $relativePath = ltrim(substr($path, strlen('storage/')), '/');
            return Storage::disk('public')->exists($relativePath)
                ? asset($path)
                : asset('images/vibebloom.png');
        }

        $relativePath = ltrim($path, '/');

        return Storage::disk('public')->exists($relativePath)
            ? asset('storage/' . $relativePath)
            : asset('images/vibebloom.png');
    }

    private function apiPlaceToCard(array $place): object
    {
        return (object) [
            'id' => $place['id'] ?? null,
            'name' => $place['name'] ?? 'Sin nombre',
            'city' => $place['city'] ?? 'Sin ciudad',
            'type' => $place['type'] ?? 'OTRO',
            'price' => $place['price'] ?? ($place['price_range'] ?? 0),
            'rating' => $place['rating'] ?? 0,
            'photo' => $this->apiPhotoUrl($place['photo_url'] ?? ($place['photo'] ?? null)),
            'is_api_place' => true,
        ];
    }

    public function index(Request $request, FastApiService $api): View
    {
        $query = trim((string) $request->query('q', ''));
        $authUser = $request->user();
        $apiPlaces = $this->syncPlatformUsers($api);
        $platformPlaceCounts = $this->platformPlaceCounts($apiPlaces);

        $users = User::query()
            ->withCount(['followers', 'following', 'places'])
            ->when($authUser, fn ($q) => $q->where('id', '!=', $authUser->id))
            ->where('profile_is_public', true)
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($inner) use ($query) {
                    $inner->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->orderByDesc('followers_count')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $users->getCollection()->transform(function (User $user) use ($platformPlaceCounts) {
            $email = mb_strtolower((string) $user->email);
            $user->platform_places_count = max((int) ($user->places_count ?? 0), (int) ($platformPlaceCounts[$email] ?? 0));

            return $user;
        });

        $followingIds = $authUser
            ? $authUser->following()->pluck('users.id')->map(fn ($id) => (int) $id)->all()
            : [];

        return view('users.index', compact('users', 'query', 'followingIds'));
    }

    public function show(Request $request, User $user, FastApiService $api): View
    {
        $authUser = $request->user();
        $apiPlaces = $this->syncPlatformUsers($api);

        $user->loadCount(['followers', 'following', 'places']);
        $isOwnProfile = $authUser && (int) $authUser->id === (int) $user->id;
        $isFollowing = $authUser ? $authUser->isFollowing($user) : false;
        $canViewPublicContent = $isOwnProfile || (bool) $user->profile_is_public;

        $followers = $user->followers()->latest('user_follows.created_at')->take(12)->get();
        $following = $user->following()->latest('user_follows.created_at')->take(12)->get();

        $localPlaces = Place::with('user')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $publishedSubmissions = PlaceSubmission::with('photos')
            ->where('user_id', $user->id)
            ->whereIn('status', ['approved', 'aprobado', 'published', 'publicado'])
            ->latest()
            ->get();

        $apiUserPlaces = $apiPlaces
            ->filter(fn ($place) => mb_strtolower((string) data_get($place, 'user.email')) === mb_strtolower((string) $user->email))
            ->map(fn ($place) => $this->apiPlaceToCard($place));

        $places = $canViewPublicContent
            ? $apiUserPlaces
                ->concat($localPlaces)
                ->concat($publishedSubmissions)
                ->unique(fn ($place) => ($place->is_api_place ?? false) ? 'api-' . $place->id : get_class($place) . '-' . $place->id)
                ->values()
            : collect();

        return view('users.show', compact(
            'user',
            'followers',
            'following',
            'places',
            'isOwnProfile',
            'isFollowing',
            'canViewPublicContent'
        ));
    }

    public function follow(Request $request, User $user): RedirectResponse
    {
        $authUser = $request->user();

        if ($authUser && (int) $authUser->id !== (int) $user->id) {
            $authUser->following()->syncWithoutDetaching([$user->id]);

            UserNotification::sendTo(
                user: $user,
                type: 'new_follower',
                title: 'Nuevo seguidor',
                body: "{$authUser->name} empezó a seguir tu perfil.",
                url: route('users.show', $authUser),
                actor: $authUser
            );
        }

        return back()->with('success', "Ahora sigues a {$user->name}.");
    }

    public function unfollow(Request $request, User $user): RedirectResponse
    {
        $authUser = $request->user();

        if ($authUser && (int) $authUser->id !== (int) $user->id) {
            $authUser->following()->detach($user->id);
        }

        return back()->with('success', "Dejaste de seguir a {$user->name}.");
    }
}
