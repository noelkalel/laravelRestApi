<?php

namespace App\Policies;

use App\User;
use App\Ads;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdsPolicy
{
    use HandlesAuthorization;
    
    public function update(User $user, Ads $ad)
    {
        return $user->id == $ad->id;        
    }

    public function delete(User $user, Ads $ad)
    {
        return $user->id == $ad->id;
    }

    public function extendAd(User $user, Ads $ad)
    {
        return $user->id == $ad->id;
    }
}
