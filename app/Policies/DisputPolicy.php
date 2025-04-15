<?php

namespace App\Policies;

use App\Models\Disput;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\HandlesAuthorization;

class DisputPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Disput $disput)
    {
        Log::debug('DisputPolicy view', [
            'user_id' => $user->id,
            'disput_id' => $disput->id,
            'disput_user_id' => $disput->user_id,
            'disput_seller_id' => $disput->seller_id,
            'is_super_admin' => $user->hasRole('super_admin'),
        ]);

        $seller_id = \App\Models\Seller::where('user_id', $user->id)->value('id');
        return $user->id === $disput->user_id || $disput->seller_id === $seller_id || $user->hasRole('super_admin');
    }
}
