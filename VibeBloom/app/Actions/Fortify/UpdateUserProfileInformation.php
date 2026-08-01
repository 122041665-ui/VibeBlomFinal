<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update($user, array $input)
    {
        Validator::make($input, [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'profile_is_public' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Escribe tu nombre.',
            'email.required' => 'Escribe tu correo electrónico.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'photo.image' => 'La foto seleccionada debe ser una imagen válida.',
            'photo.mimes' => 'La foto debe ser JPG, PNG o WEBP.',
            'photo.max' => 'La foto debe pesar como máximo 5 MB.',
        ])->validateWithBag('updateProfileInformation');

        if (!empty($input['photo'])) {
            $this->updateProfilePhoto($user, $input['photo']);
        }

        $user->forceFill([
            'name'  => $input['name'],
            'email' => $input['email'],
            'profile_is_public' => array_key_exists('profile_is_public', $input)
                ? (bool) $input['profile_is_public']
                : (bool) $user->profile_is_public,
        ])->save();
    }

    protected function updateProfilePhoto($user, $photo)
    {
        if ($photo instanceof TemporaryUploadedFile) {

            $photoContents = $photo->get();
            $photoName = $photo->getClientOriginalName();

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $photo->store('profile-photos', 'public');

            $user->forceFill([
                'profile_photo_path' => $path,
            ])->save();

            $token = session('access_token')
                ?? data_get(session('user'), 'access_token')
                ?? data_get(session('api'), 'access_token')
                ?? data_get(session('api_user'), 'access_token');

            if (is_string($token) && trim($token) !== '') {
                try {
                    $response = Http::withToken($token)
                        ->attach('photo', $photoContents, $photoName)
                        ->post(rtrim(config('services.fastapi.url'), '/') . '/users/me/profile-photo');

                    if ($response->successful() && is_string($response->json('profile_photo_url'))) {
                        $user->forceFill([
                            'external_profile_photo_url' => $response->json('profile_photo_url'),
                        ])->save();
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
    }
}
