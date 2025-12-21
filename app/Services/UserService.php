<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService extends BaseService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers($perPage)
    {
        return $this->userRepository->paginateWithRoles($perPage);
    }

    public function getUserProfile($userId)
    {
        return $this->userRepository->findUserById($userId);
    }

    /**
     * Logic tạo user và xác định role
     */
    public function createUser(array $data)
    {
        // 1. Xác định Role ID (role_code)
        // Mặc định là user (2)
        $roleId = 2; 

        if ($data['role'] === 'admin') {
            $roleId = 1; // Admin
        } elseif ($data['role'] === 'user' && !empty($data['is_display_user'])) {
            $roleId = 3; // Display User (Nếu chọn User + Checkbox Display)
        }

        // 2. Chuẩn bị dữ liệu insert bảng t_users
        $userData = [
            'user_id' => $data['user_id'],
            'display_name' => $data['display_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // Hash password
            // Các trường mặc định khác nếu cần
        ];

        // 3. Gọi Repository để lưu transaction
        return $this->userRepository->createWithRole($userData, $roleId);
    }

    /**
     * Logic update user
     */
    public function updateUser($id, array $data)
    {
        // 1. Tìm User
        $user = $this->userRepository->findUserById($id);
        if (!$user) {
            throw new \Exception("User not found");
        }

        // 2. Xác định Role ID mới (Logic giống Create)
        $roleId = 2; // Default User
        if ($data['role'] === 'admin') {
            $roleId = 1;
        } elseif ($data['role'] === 'user' && !empty($data['is_display_user'])) {
            $roleId = 3;
        }

        // 3. Chuẩn bị dữ liệu update
        $updateData = [
            'display_name' => $data['display_name'],
            'email' => $data['email'],
        ];

        // Chỉ update password nếu người dùng có nhập
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        // 4. Gọi Repository để update Transaction
        return $this->userRepository->updateWithRole($user, $updateData, $roleId);
    }

    /**
     * Logic xóa user
     */
    public function deleteUser($id)
    {
        // Gọi repository xóa
        // Hàm delete() của Eloquent trả về true/false hoặc số dòng đã xóa
        $deleted = $this->userRepository->delete($id);

        if (!$deleted) {
            throw new \Exception("Failed to delete user or user not found.");
        }

        return true;
    }
}