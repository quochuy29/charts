<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

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

    public function paginateWithRoles($perPage)
    {
        return $this->model->with('roles')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Tạo User và gán Role
     */
    public function createWithRole($userData, $roleId)
    {
        return DB::transaction(function () use ($userData, $roleId) {
            // 1. Tạo User trong bảng t_users
            $user = $this->model->create($userData);

            // 2. Gán Role vào bảng trung gian t_user_roles
            // Nhờ đã config quan hệ roles() chính xác ở bước trước (dùng user_id string), 
            // hàm attach sẽ tự động lấy user_id string của user insert vào bảng pivot.
            $user->roles()->attach($roleId);

            return $user;
        });
    }

    /**
     * Update User và Role trong Transaction
     */
    public function updateWithRole($user, $updateData, $roleId)
    {
        return DB::transaction(function () use ($user, $updateData, $roleId) {
            // 1. Update thông tin cơ bản trong bảng t_users
            $user->update($updateData);

            // 2. Update Role trong bảng trung gian t_user_roles
            // Hàm sync sẽ xóa các role cũ và gán role mới vào
            // Lưu ý: Quan hệ roles() trong Model User phải được định nghĩa đúng khóa (user_id string)
            $user->roles()->sync([$roleId]);

            // Trả về user kèm role mới để FE cập nhật UI
            return $user->load('roles');
        });
    }

    /**
     * Xóa User theo ID
     */
    public function delete($id)
    {
        // Tìm user theo ID (Primary Key) và xóa
        $user = $this->model->find($id);
        
        if ($user) {
            return $user->delete();
        }
        
        return false;
    }
}