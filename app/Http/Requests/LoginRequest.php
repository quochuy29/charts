<?php

namespace App\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'string'], // Đổi email thành user_id theo yêu cầu
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Xử lý xác thực và Rate Limiting
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Kiểm tra thông tin đăng nhập
        // Lấy user để check cờ is_persistent_login (nếu field này nằm trong DB)
        // Hoặc lấy từ request nếu field này gửi từ form lên.
        // Ở đây giả sử validate credentials trước.
        if (! Auth::attempt($this->only('user_id', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey()); // Tăng bộ đếm sai

            throw ValidationException::withMessages([
                'user_id' => trans('auth.failed'),
            ]);
        }

        // Nếu login thành công -> Xóa bộ đếm rate limit
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Kiểm tra xem user có bị khóa không
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) { // Giới hạn 5 lần
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'user_id' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Tạo key định danh cho RateLimiter (dựa trên user_id và IP)
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('user_id')).'|'.$this->ip());
    }
}