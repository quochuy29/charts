<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\UserService;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    protected $authService;
    protected $userService; // Khai báo

    // Inject UserService vào constructor
    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function login(LoginRequest $request)
    {
        // 1. Validate & Check Rate Limit (Đã xử lý trong LoginRequest::authenticate)
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // 2. Xử lý "Vô thời hạn"
        // Giả sử logic check persistent nằm ở field 'is_persistent_login' trong bảng users
        // Hoặc gửi lên từ client. Ở đây tôi ưu tiên check từ User Model trong DB như yêu cầu.
        
        $isPersistent = (bool) $user->is_persistent_login; 
        
        // Cần Logout và Login lại với tham số remember = true/false để Laravel set cookie đúng
        // Lưu ý: Auth::attempt ở trên chỉ check pass, giờ ta set session chính thức.
        Auth::login($user, $isPersistent);

        // 3. Chuẩn bị dữ liệu trả về cho FE
        // Lấy thời gian hết hạn session từ config (phút) -> đổi ra mili giây
        $sessionLifetime = config('session.lifetime') * 60 * 1000;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'is_persistent' => $isPersistent,
            // Nếu persistent, trả về null hoặc số cực lớn. Nếu không, trả về thời gian session.
            'session_expires_in_ms' => $isPersistent ? null : $sessionLifetime,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
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