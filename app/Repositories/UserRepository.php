<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;

class UserRepository extends BaseRepository
{
    public function getModel()
    {
        return User::class;
    }
}