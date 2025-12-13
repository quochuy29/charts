<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function getModel()
    {
        return User::class;
    }

    public function findUserById($id)
    {
        return $this->model->find($id);
    }
}