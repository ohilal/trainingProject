<?php

namespace App\Services\Units\Coins;

use App\Models\Badge;
use App\Models\User;

class UserCoins
{

    protected function userUpdateCoins(User $user, int $coins): bool
    {
        if ($coins == 0)
            return false;

        $user->coins = $user->coins + ($coins);
        $user->save();

        return true;
    }


    /**
     * getUserBadge
     *
     * @param  User $user
     * @return Badge
     */
    public function getUserBadge(User $user): Badge|null
    {


          // Tier whose [min, max) range contains the user's coin balance.
        $badge = Badge::where('min_coins', '<=', $user->coins)
            ->where('max_coins', '>', $user->coins)
            ->orderBy('min_coins', 'desc')
            ->first();

        // Gap between tiers: fall back to the highest badge the user has earned.
        if (empty($badge)) {
            $badge = Badge::where('min_coins', '<=', $user->coins)
                ->orderBy('min_coins', 'desc')
                ->first();
        }

        // Balance below every tier (or table empty): lowest tier / null.
        // Never create factory rows here — that would pollute production data.
        if (empty($badge)) {
            $badge = Badge::orderBy('min_coins', 'asc')->first();
        }

        return $badge;
    }
}