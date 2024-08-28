<?php

namespace App\Http\Controllers\Api;

use App\Events\UserLoggedOut;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class AuthController extends Controller
{
    /**
     * Hàm đăng nhập
     * @param Request $request
     * @return $user
     * CreatedBy: youngbachhh (31/03/2024)
     */
    // public function login(Request $request)
    // {

    //     $success = Auth::attempt($request->only('email', 'password'));

    //     if (!$success) {
    //         return response()->json(['message' => 'Thông tin đăng nhập không đúng!'], 401);
    //     }
    //     // kiểm tra user còn quyền vào tài khoản không ->where('is_active', 1)
    //     $user = User::where('email', $request->email)->where('is_active', 1)->first();

    //     $token = $user->createToken($request->email);

    //     $user->token = $token->plainTextToken;

    //     return response()->json($user, 200);
    // }

    public function login(Request $request)
    {
        // Xác thực thông tin đăng nhập
        $success = Auth::attempt($request->only('email', 'password'));

        if (!$success) {
            return response()->json(['message' => 'Thông tin đăng nhập không đúng!'], 401);
        }

        // Kiểm tra user còn quyền vào tài khoản không ->where('is_active', 1)
        $user = User::where('email', $request->email)->where('is_active', 1)->first();

        if (!$user) {
            return response()->json(['message' => 'Tài khoản không hoạt động hoặc không tồn tại!'], 404);
        }
            $currentDateTime = Carbon::now();
      
            $user->update([
                'is_login' => $currentDateTime->timestamp,
            ]);
                
           
          // Kiểm tra nếu user đang có session hoạt động khác
            event(new UserLoggedOut($user->id));
        

        // Tạo session mới cho user B và xóa session cũ của user A
    //    Auth::logoutOtherDevices($request->password);
        // Xóa tất cả các token hiện tại của người dùng để đảm bảo chỉ có một phiên đăng nhập
        $user->tokens()->delete();

        // Tạo token mới
        $token = $user->createToken($request->email);

        // Trả về token mới cho người dùng
        $user->token = $token->plainTextToken;

        return response()->json($user, 200);
    }
    public function updateLoginStatus(Request $request)
    {
        $userId = $request->userId;
        $is_login = $request->is_login;

        $user   =   User::where('id', $userId)->first();
        if(!$user){
            return response()->json(['status' => 'error']);
        }
        if($user->is_login > $is_login){
            return response()->json(['status' => 'error']);
        }

        return response()->json(['success' => true]);
      
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
