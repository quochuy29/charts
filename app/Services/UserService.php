<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService extends BaseService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserProfile($userId)
    {
        $user = $this->userRepository->findUserById($userId);
        
        // Logic phụ: Nếu display_name chưa set thì lấy name hoặc email làm fallback
        if (!$user->display_name) {
            $user->display_name = $user->name ?? explode('@', $user->email)[0];
        }

        return $user;
    }
}