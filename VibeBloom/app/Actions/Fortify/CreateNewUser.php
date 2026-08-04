<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\FastApiService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'terms.required' => 'Debes aceptar los términos, las condiciones del servicio y el aviso de privacidad.',
            'terms.accepted' => 'Debes aceptar los términos, las condiciones del servicio y el aviso de privacidad.',
        ])->validate();

        // Las pruebas de Fortify trabajan exclusivamente con su base efímera.
        if (app()->environment('testing')) {
            return $this->createLocalUser($input);
        }

        try {
            $api = app(FastApiService::class);
            $registerResponse = $api->post('/auth/register', [
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'role' => 'user',
            ]);
        } catch (\Throwable $exception) {
            Log::error('No fue posible conectar con FastAPI durante el registro', [
                'email' => $input['email'],
                'message' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'email' => 'No fue posible crear la cuenta. Inténtalo de nuevo.',
            ]);
        }

        if (! $registerResponse->successful()) {
            Log::warning('FastAPI rechazó el registro', [
                'email' => $input['email'],
                'status' => $registerResponse->status(),
                'body' => $registerResponse->body(),
            ]);

            throw ValidationException::withMessages([
                'email' => $registerResponse->status() === 400
                    ? 'El correo ya está registrado.'
                    : 'No fue posible crear la cuenta. Inténtalo de nuevo.',
            ]);
        }

        $registerData = $registerResponse->json();
        $token = is_array($registerData) ? ($registerData['access_token'] ?? null) : null;
        $apiUser = is_array($registerData) && is_array($registerData['user'] ?? null)
            ? $registerData['user']
            : $registerData;

        // Compatibilidad durante un despliegue gradual: la API anterior no
        // entregaba el JWT al registrar y todavía requería un segundo login.
        if (! is_string($token) || trim($token) === '') {
            try {
                $loginResponse = $api->post('/auth/login', [
                    'email' => $input['email'],
                    'password' => $input['password'],
                ]);
            } catch (\Throwable $exception) {
                Log::error('La cuenta fue creada en FastAPI pero falló la conexión de inicio de sesión', [
                    'email' => $input['email'],
                    'message' => $exception->getMessage(),
                ]);

                throw ValidationException::withMessages([
                    'email' => 'La cuenta fue creada, pero no se pudo iniciar sesión. Inténtalo nuevamente.',
                ]);
            }

            if (! $loginResponse->successful()) {
                Log::error('La cuenta fue creada en FastAPI pero no se pudo iniciar sesión', [
                    'email' => $input['email'],
                    'status' => $loginResponse->status(),
                    'body' => $loginResponse->body(),
                ]);

                throw ValidationException::withMessages([
                    'email' => 'La cuenta fue creada, pero no se pudo iniciar sesión. Inténtalo nuevamente.',
                ]);
            }

            $loginData = $loginResponse->json();
            $token = is_array($loginData) ? ($loginData['access_token'] ?? null) : null;
        }

        if (! is_string($token) || trim($token) === '') {
            throw ValidationException::withMessages([
                'email' => 'La API no devolvió una sesión válida.',
            ]);
        }

        $user = $this->createLocalUser($input, is_array($apiUser) ? $apiUser : []);

        session()->put('access_token', $token);
        session()->put('api_user', is_array($apiUser) ? $apiUser : []);

        return $user;
    }

    /**
     * @param  array<string, string>  $input
     * @param  array<string, mixed>  $apiUser
     */
    private function createLocalUser(array $input, array $apiUser = []): User
    {
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'platform_user_id' => $apiUser['id'] ?? null,
            'external_profile_photo_url' => $apiUser['profile_photo_url'] ?? null,
        ]);
    }
}
