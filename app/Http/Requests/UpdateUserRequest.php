<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy ID của user đang được edit từ route (ví dụ: /api/users/{user})
        // Lưu ý: Tham số trong route resource mặc định là tên model ('user')
        $userId = $this->route('user'); 

        return [
            // User ID thường không cho sửa, nên không validate unique ở đây (hoặc ignore)
            // 'user_id' => 'required|string...', 
            
            'display_name' => 'required|string|max:255',
            
            // Email: Check unique bảng t_users nhưng BỎ QUA user hiện tại
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('t_users', 'email')->ignore($userId),
            ],

            // Password: Cho phép null (nghĩa là không đổi pass)
            'password' => 'nullable|string|min:8',
            
            'role' => 'required|string|in:admin,user',
            'is_display_user' => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'display_name' => '表示名',
            'email' => 'メールアドレス',
            'password' => 'パスワード',
            'role' => '役割',
            'is_display_user' => '表示用ユーザー',
        ];
    }
}