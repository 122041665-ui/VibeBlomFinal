<?php

namespace App\Http\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateProfileInformationForm extends Component
{
    use WithFileUploads;

    public $state = [];
    public $photo;
    public $user;

    protected $rules = [
        'state.name' => 'required|string|max:255',
        'state.email' => 'required|email|max:255',
        'state.profile_is_public' => 'nullable|boolean',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
    ];

    protected $messages = [
        'state.name.required' => 'Escribe tu nombre.',
        'state.email.required' => 'Escribe tu correo electrónico.',
        'state.email.email' => 'Escribe un correo electrónico válido.',
        'photo.image' => 'La foto seleccionada debe ser una imagen válida.',
        'photo.mimes' => 'La foto debe ser JPG, PNG o WEBP.',
        'photo.max' => 'La foto debe pesar como máximo 5 MB.',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        $this->state = $this->user->withoutRelations()->toArray();
        $this->state['profile_is_public'] = (bool) ($this->state['profile_is_public'] ?? true);
    }

    public function updateProfileInformation(UpdatesUserProfileInformation $updater)
    {
        $this->validate();

        $data = [
            'name'  => $this->state['name'],
            'email' => $this->state['email'],
            'profile_is_public' => (bool) ($this->state['profile_is_public'] ?? false),
        ];

        if ($this->photo) {
            $data['photo'] = $this->photo; // 🔥 Esto asegura que se envía como UploadedFile
        }

        $updater->update($this->user, $data);

        $this->emit('saved');
        $this->emit('refresh-navigation-menu');
    }

    public function deleteProfilePhoto()
    {
        $this->user->deleteProfilePhoto();
        $this->user->forceFill(['external_profile_photo_url' => null])->save();

        $token = session('access_token')
            ?? data_get(session('user'), 'access_token')
            ?? data_get(session('api'), 'access_token')
            ?? data_get(session('api_user'), 'access_token');

        if (is_string($token) && trim($token) !== '') {
            try {
                Http::withToken($token)
                    ->delete(rtrim(config('services.fastapi.url'), '/') . '/users/me/profile-photo');
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->emit('refresh-navigation-menu');
    }

    public function render()
    {
        return view('profile.update-profile-information-form');
    }
}
