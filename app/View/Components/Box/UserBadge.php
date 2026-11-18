<?php

namespace App\View\Components\Box;

use App\Models\Badge;
use App\Models\User;
use App\Services\Units\Coins\UserCoins;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Storage;

class UserBadge extends Component
{

    public User $user;
    public int $coins = 0;
    public ?Badge $badge = null;
    public string $badgeUrl;
    public string $badgeTitle;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(User $user, UserCoins $userCoins)
    {
        $this->user = $user;
        $this->coins = $user->coins;
        $this->badge = $userCoins->getUserBadge($user);
        $this->badgeTitle = $this->badge?->title ?? (string) __('Default badge');
        $this->badgeUrl = $this->getBadgeUrl();
    }

    private function getBadgeUrl(): string
    {
        $defaultFile = 'badges/Owgzv1sggpfCJPYOW0B2ezVQOno0xBsphPdQpgtc.png';

        if ($this->badge && $this->badge->file) {
            $badgeFile = ltrim($this->badge->file, '/');
            $candidateFiles = str_contains($badgeFile, '/')
                ? [$badgeFile]
                : [$badgeFile, 'badges/' . $badgeFile];

            foreach ($candidateFiles as $candidateFile) {
                if (Storage::disk('public')->exists($candidateFile)) {
                    return asset('storage/' . $candidateFile);
                }
            }
        }

        return asset('storage/' . $defaultFile);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.box.user-badge');
    }
}
