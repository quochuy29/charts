<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class ExtendPersistentSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Chỉ áp dụng nếu user đã đăng nhập
        // $request->user() lấy user từ guard hiện tại (sanctum) -> OK
        $user = $request->user();

        if ($user && $user->isPersistent()) { // Check role 3
            
            // 2. SỬA LỖI Ở ĐÂY:
            // Gọi cụ thể guard 'web' để lấy tên cookie Remember Me (vì sanctum guard không có method này)
            $cookieName = Auth::guard('web')->getRecallerName();

            // 3. Nếu request có gửi lên cookie này (tức là đang dùng Remember Me)
            if ($request->hasCookie($cookieName)) {
                
                // Lấy giá trị token hiện tại
                $cookieValue = $request->cookie($cookieName);
                
                // 4. Gia hạn lại cookie này thêm 5 năm (2628000 phút) tính từ BÂY GIỜ
                // Queue cookie vào response trả về cho client
                Cookie::queue(
                    $cookieName,
                    $cookieValue,
                    2628000 // 5 năm
                );
            }
        }

        return $response;
    }
}