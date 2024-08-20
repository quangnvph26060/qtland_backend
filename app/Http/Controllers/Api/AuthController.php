<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    /**
     * Hàm đăng nhập
     * @param Request $request
     * @return $user
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function login(Request $request)
    {

        $success = Auth::attempt($request->only('email', 'password'));

        if (!$success) {
            return response()->json(['message' => 'Thông tin đăng nhập không đúng!'], 401);
        }
        // kiểm tra user còn quyền vào tài khoản không ->where('is_active', 1)
        $user = User::where('email', $request->email)->where('is_active', 1)->first();

        $token = $user->createToken($request->email);

        $user->token = $token->plainTextToken;

        return response()->json($user, 200);
    }

    public function refreshToken(Request $request)
    {
        $refreshToken = $request->header('Refresh-Token');

        // Giả sử bạn lưu trữ refresh token trong bảng users hoặc một bảng khác
        // và có một cột là `refresh_token` và `refresh_token_expires_at`
        $user = User::where('refresh_token', $refreshToken)
            ->where('refresh_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Refresh token không hợp lệ hoặc đã hết hạn'], 401);
        }

        // Tạo token mới
        $token = $user->createToken($user->email)->plainTextToken;

        // Cập nhật refresh token (tùy chọn)
        // $user->updateRefreshToken();

        return response()->json(['access_token' => $token]);
    }

    /**
     * Hàm đăng xuất
     * @param Request $request
     * @return $message
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function logout(Request $request)
    {
        // Chỉ xóa token hiện tại
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Đăng xuất thành công'], 200);
    }


    public function me(Request $request)
    {
        return response()->json($request->user(), 200);
    }
}
