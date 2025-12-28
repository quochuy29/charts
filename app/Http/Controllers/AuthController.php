<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\UserService;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    protected $authService;
    protected $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function login(LoginRequest $request)
    {
        // 1. Validate & Rate Limiting (Giữ nguyên logic cũ trong LoginRequest)
        $request->authenticate();

        // 2. Lấy User
        $user = User::where('user_id', $request->user_id)->first();

        // 3. Xử lý logic Session & Remember Me
        // Nếu role_code = 3 (isPersistent) -> $remember = true (Vô hạn/5 năm)
        // Nếu role khác -> $remember = false (Hết session tắt browser là out hoặc theo lifetime)
        $remember = $user->isPersistent();

        // 4. Đăng nhập qua Session Manager
        // Hàm này sẽ tự tạo session record trong DB và trả về cookie
        Auth::login($user, $remember);

        // 5. Regenerate Session ID để chống tấn công Session Fixation
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load('roles'),
            // Không còn trả về access_token/refresh_token nữa
        ]);
    }

    public function logout(Request $request)
    {
        // Hủy session trong DB
        Auth::guard('web')->logout();

        // Invalidate session hiện tại
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function getUserLogin(Request $request)
    {
        $userId = Auth::id();
        $user = $this->userService->getUserProfile($userId);

        return response()->json([
            'user' => $user
        ]);
    }
}