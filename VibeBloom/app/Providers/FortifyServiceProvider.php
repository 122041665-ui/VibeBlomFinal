<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\LogoutResponse;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use App\Services\FastApiService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::authenticateUsing(function (Request $request) {
            $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

            // Las pruebas de Fortify usan la base efímera local. En la aplicación
            // real FastAPI continúa siendo la autoridad de autenticación.
            if (app()->environment('testing')) {
                $user = User::where('email', $request->email)->first();

                return $user && Hash::check($request->password, $user->password)
                    ? $user
                    : null;
            }

            try {
                $api = app(FastApiService::class);
                $response = $api->post('/auth/login', [
                    'email' => $request->email,
                    'password' => $request->password,
                ]);
            } catch (\Throwable $e) {
                Log::error('Error al conectar con FastAPI durante login', [
                    'email' => $request->email,
                    'message' => $e->getMessage(),
                ]);

                return null;
            }

            // Repara cuentas creadas por versiones anteriores únicamente en
            // SQLite: valida primero su hash local y luego las registra en MySQL.
            if ($response->status() === 401) {
                $localUser = User::where('email', $request->email)->first();

                if ($localUser && Hash::check($request->password, $localUser->password)) {
                    try {
                        $registerResponse = $api->post('/auth/register', [
                            'name' => $localUser->name,
                            'email' => $localUser->email,
                            'password' => $request->password,
                            'role' => 'user',
                        ]);

                        if ($registerResponse->successful()) {
                            $registeredUser = $registerResponse->json();
                            $localUser->platform_user_id = is_array($registeredUser)
                                ? ($registeredUser['id'] ?? $localUser->platform_user_id)
                                : $localUser->platform_user_id;
                            $localUser->save();

                            $response = $api->post('/auth/login', [
                                'email' => $request->email,
                                'password' => $request->password,
                            ]);
                        }
                    } catch (\Throwable $e) {
                        Log::error('No fue posible migrar la cuenta local a FastAPI', [
                            'email' => $request->email,
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
            }

            if (! $response->successful()) {
                Log::warning('FastAPI rechazó el login', [
                    'email' => $request->email,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json();
            $apiUser = is_array($data) ? ($data['user'] ?? null) : null;
            $token = is_array($data) ? ($data['access_token'] ?? null) : null;

            if (! is_array($apiUser) || ! is_string($token) || trim($token) === '') {
                Log::warning('FastAPI respondió login sin user o token válidos', [
                    'email' => $request->email,
                    'payload' => $data,
                ]);

                return null;
            }

            $email = $apiUser['email'] ?? null;
            $name = $apiUser['name'] ?? 'Usuario';
            $role = $apiUser['role'] ?? 'user';

            if (! is_string($email) || trim($email) === '') {
                Log::warning('FastAPI respondió login sin email válido', [
                    'payload' => $apiUser,
                ]);

                return null;
            }

            $user = User::firstOrNew(['email' => $email]);
            $user->name = $name;
            $user->platform_user_id = $apiUser['id'] ?? $user->platform_user_id;
            $user->external_profile_photo_url = $apiUser['profile_photo_url']
                ?? $user->external_profile_photo_url;

            if (property_exists($user, 'role') || array_key_exists('role', $user->getAttributes()) || $user->getConnection()->getSchemaBuilder()->hasColumn($user->getTable(), 'role')) {
                $user->role = $role;
            }

            $user->password = Hash::make($request->password);
            $user->save();

            $request->session()->put('access_token', $token);
            $request->session()->put('api_user', $apiUser);
            $request->session()->save();

            return $user;
        });

        RateLimiter::for('login', function (Request $request) {
            $key = Str::lower($request->email) . '|' . $request->ip();
            return Limit::perMinute(5)->by($key);
        });

        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
    }
}
