<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class Profile extends Component
{

    public User $user;
    public $avatars = [];

    public function mount(User $user)
    {
        $this->user = $user;

        $avatars = array_map(function ($file) {
            return $file->getFilename();
        }, File::files(public_path('img/profiles')));

        natcasesort($avatars);
        $this->avatars = array_values($avatars);
    }

    public function render()
    {
        return view('livewire.user.profile');
    }
}
