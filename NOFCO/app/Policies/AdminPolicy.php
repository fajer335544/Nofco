<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class AdminPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function useAdministration(User $user)
    {

        return  (Auth::user()->type == 1);

    }

    public function edit(User $model)
    {
        return  (Auth::user()->id == $model->id);
    }

}
