<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Voucer;
use Illuminate\Auth\Access\Response;

class VoucerPolicy
{

   public function modify(User $user, Voucer $voucer): Response
    {
        return $user->id === $voucer->user_id
            ? Response::allow()
            : Response::deny('You can only modify your own voucers');
    }
}
