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
            'photo' => ['nullable', 'image', 'max:2048'],
            'profile_is_public' => ['nullable', 'boolean'],
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
                    Http::withToken($token)
                        ->attach('photo', fopen($photo->getRealPath(), 'r'), $photo->getClientOriginalName())
                        ->post(rtrim(config('services.fastapi.url'), '/') . '/users/me/profile-photo');
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
    }
}
