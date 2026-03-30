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
