<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\CreateUserRequest; // <--- Import Request mới
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $users = $this->userService->getAllUsers(5);
        return response()->json($users);
    }

    /**
     * Tạo mới User
     * Sử dụng CreateUserRequest để validate tự động
     */
    public function store(CreateUserRequest $request) // <--- Type hint class Request mới
    {
        // Không cần $request->validate([...]) nữa
        // Dữ liệu đã được validate & lọc sạch sẽ lấy qua $request->validated()
        
        try {
            // Gọi Service xử lý logic nghiệp vụ
            $user = $this->userService->createUser($request->validated());

            return response()->json([
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to create user'], 500);
        }
    }

    /**
     * Cập nhật User
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        try {
            // Gọi Service xử lý
            $user = $this->userService->updateUser($id, $request->validated());

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error("Update User Error: " . $e->getMessage());
            return response()->json(['message' => 'Failed to update user'], 500);
        }
    }

    /**
     * Xóa User
     */
    public function destroy(string $id)
    {
        try {
            // Kiểm tra không cho phép xóa chính mình (nếu cần)
            if (auth()->id() == $id) {
                return response()->json(['message' => '自分自身を削除することはできません。'], 403);
            }

            $this->userService->deleteUser($id);

            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Delete User Error: " . $e->getMessage());
            return response()->json(['message' => 'Failed to delete user'], 500);
        }
    }
}