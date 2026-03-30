<?php

namespace App\Http\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
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
        'photo' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        $this->state = $this->user->withoutRelations()->toArray();
    }

    public function updateProfileInformation(UpdatesUserProfileInformation $updater)
    {
        $this->validate();

        $data = [
            'name'  => $this->state['name'],
            'email' => $this->state['email'],
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

        $this->emit('refresh-navigation-menu');
    }

    public function render()
    {
        return view('profile.update-profile-information-form');
    }
}
