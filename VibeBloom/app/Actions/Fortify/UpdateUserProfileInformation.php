<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
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
        ])->validateWithBag('updateProfileInformation');

        if (!empty($input['photo'])) {
            $this->updateProfilePhoto($user, $input['photo']);
        }

        $user->forceFill([
            'name'  => $input['name'],
            'email' => $input['email'],
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
        }
    }
}
