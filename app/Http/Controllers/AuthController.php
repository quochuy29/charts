<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\UserService;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        // 1. Validate
        $request->authenticate();
        $user = User::where('user_id', $request->user_id)->first();

        // 2. Xử lý Logic thời hạn (Persistent)
        $isPersistent = (bool) $user->is_persistent_login;

        // Cấu hình thời gian
        // Access Token: Luôn ngắn (ví dụ 2 tiếng) để bảo mật
        $accessTokenExpiration = Carbon::now()->addMinutes(config('sanctum.expiration', 120));
        
        // Refresh Token: Dài (5 năm nếu persistent, hoặc 1 ngày nếu không)
        $refreshTokenExpiration = $isPersistent 
            ? Carbon::now()->addYears(5) 
            : Carbon::now()->addDay();

        // 3. Tạo Access Token (Ngắn hạn) - Chỉ có quyền truy cập API
        $accessToken = $user->createToken('access_token', ['access-api'], $accessTokenExpiration);

        // 4. Tạo Refresh Token (Dài hạn) - Chỉ có quyền cấp lại token
        $refreshToken = $user->createToken('refresh_token', ['issue-access-token'], $refreshTokenExpiration);

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            // Trả về cả 2 token
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => $accessTokenExpiration->timestamp, // FE có thể dùng để căn thời gian refresh chủ động
        ]);
    }

    /**
     * Làm mới Token (Rotation)
     */
    public function refreshToken(Request $request)
    {
        // Lấy refresh token từ request
        $refreshTokenString = $request->input('refresh_token');
        
        // Tìm token trong DB (Sanctum lưu hashed, nên ta cần tìm qua model nếu muốn check kỹ, 
        // nhưng đơn giản nhất là dùng user() từ middleware auth:sanctum với token đó)
        
        // Lưu ý: Route này cần được bảo vệ bởi auth:sanctum, client sẽ gửi Refresh Token làm Bearer Token
        $user = $request->user();
        $currentToken = $user->currentAccessToken();

        // Kiểm tra Token Ability: Chỉ token có quyền 'issue-access-token' mới được refresh
        if (!$currentToken->can('issue-access-token')) {
            return response()->json(['message' => 'Invalid token ability'], 403);
        }

        // --- LOGIC ROTATION (Gia hạn vô hạn) ---
        // 1. Thu hồi Refresh Token cũ (để tránh dùng lại)
        $currentToken->delete();

        // 2. Xác định lại thời gian (logic giống lúc login)
        $isPersistent = (bool) $user->is_persistent_login;
        
        $accessTokenExpiration = Carbon::now()->addMinutes(config('sanctum.expiration', 120));
        $refreshTokenExpiration = $isPersistent 
            ? Carbon::now()->addYears(5) // Gia hạn thêm 5 năm nữa từ thời điểm này
            : Carbon::now()->addDay();

        // 3. Cấp cặp token mới
        $newAccessToken = $user->createToken('access_token', ['access-api'], $accessTokenExpiration);
        $newRefreshToken = $user->createToken('refresh_token', ['issue-access-token'], $refreshTokenExpiration);

        return response()->json([
            'access_token' => $newAccessToken->plainTextToken,
            'refresh_token' => $newRefreshToken->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        // Thu hồi token hiện tại
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
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