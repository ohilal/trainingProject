<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{

    public User $user;
    public $avatars = [];
    public $selectedAvatar;
    public $saved = false;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->avatars = $this->getAvatarFilenames();

        $this->selectedAvatar = in_array($user->avatar, $this->avatars, true)
            ? $user->avatar
            : ($this->avatars[0] ?? null);
    }

    public function updatedSelectedAvatar()
    {
        $this->saved = false;
    }

    public function save()
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->is($this->user), 403);

        $this->validate([
            'selectedAvatar' => ['required', Rule::in($this->getAvatarFilenames())],
        ]);

        $user->avatar = $this->selectedAvatar;
        $user->save();
        $this->user = $user;
        $this->saved = true;
    }

    private function getAvatarFilenames(): array
    {
        $avatars = array_map(function ($file) {
            return $file->getFilename();
        }, File::files(public_path('img/profiles')));

        natcasesort($avatars);

        return array_values($avatars);
    }

    public function render()
    {
        return view('livewire.user.profile');
    }
}
