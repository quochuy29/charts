<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cho phép mọi user đã đăng nhập thực hiện
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|string|max:50|unique:t_users,user_id',
            'display_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:t_users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,user',
            'is_display_user' => 'boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'ユーザーID',
            'display_name' => '表示名',
            'email' => 'メールアドレス',
            'password' => 'パスワード',
            'role' => '役割',
            'is_display_user' => '表示用ユーザー',
        ];
    }

    /**
     * Get custom messages for validator errors (Optional)
     */
    public function messages(): array
    {
        return [
            'user_id.unique' => 'このユーザーIDは既に使用されています。',
            'email.unique' => 'このメールアドレスは既に使用されています。',
        ];
    }
}