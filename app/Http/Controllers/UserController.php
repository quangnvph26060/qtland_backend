<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{


    /**
     * Hàm lấy danh sách người dùng
     * @param
     * @return $users
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function index()
    {
        return User::withCount('post')->get();
    }

    public function userrole()
    {
        return User::withCount('post')
        ->whereNotIn('role_id', [1, 6])
        ->get();
    }

    /**
     * Hàm tạo mới người dùng
     * @param
     * @return $users
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function create()
    {
        //
    }

    /**
     * Hàm lưu người dùng mới
     * @param Request $request
     * @return $users
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required'

            ],
            [
                'name.required' => 'Không được bỏ trống tên',
                'email.required' => 'Không được bỏ trống email',
                'email.email' => 'Email không đúng định dạng',
                'email.unique' => 'Email đã tồn tại',
                'password.required' => 'Không được bỏ trống mật khẩu'

            ]
        );

        $user = User::create(
            [
                'name' => $request['name'],
                'email' => $request['email'],
                'cccd' => $request['cccd'],
                'birthday' => $request['birthday'],
                'phone' => $request['phone'],
                'address' => $request['address'],
                'workunit' => $request['workunit'],
                'role_id' => ($request['role_id'] == null ? 3 : (int) $request['role_id']),
                'is_active' => ($request['is_active'] == null ? 1 : (int) $request['is_active']),
                'password' =>  Hash::make($request['password']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        return response()->json(['message' => 'Thêm mới thành công'], 201);
    }

    /**
     * Hàm lấy thông tin người dùng theo id
     * @param $id
     * @return $users
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function show($id)
    {
        //
        return User::findOrFail($id);
    }

    public function getName($id)
    {
        return User::findOrFail($id)->name;
    }

    /**
     * Hàm chỉnh sửa người dùng
     * @param $user
     * @return $users
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function edit(User $user)
    {
        //



    }

    /**
     * Hàm cập nhật người dùng
     * @param Request $request, $id
     * @return message
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function update(Request $request, $id)
    {

        $validatedData = $request->validate(
            [
                'name' => 'required',
                'password' => 'nullable'

            ],
            [
                'name.required' => 'Không được bỏ trống tên',

            ]
        );

        $user = User::find($id);

        $user->update([
            "name" => $request->name,
            "email" => $request->email,
            "password" => (empty($request->password) ? $user->password : Hash::make($request->password)),
            "role_id" => $request->role_id,
            "is_active" => $request->is_active,
            'cccd' => $request->cccd,
            'phone' => $request->phone,
            'address' => $request->address,
            'workunit' => $request->workunit,
            'birthday' => $request->birthday,
            "updated_at" => date('Y-m-d H:i:s'),
        ]);

        return response()->json(['message' => 'Cập nhật thành công'], 200);
    }


    /**
     * Hàm xóa người dùng
     * @param $user
     * @return message
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'Xóa thành công'], 200);
    }

    public function updateAvatar(Request $request)
    {
        $user = User::find($request->id);
        $appUrl = env('APP_URL');
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có
            $path = $request->file('avatar')->store('public/upload/images/avatar');
            $relativePath = str_replace('public/', 'storage/', $path); // Chuyển đổi đường dẫn lưu trữ sang URL
            $user->avatar = $appUrl . '/' . $relativePath;
        }

        $user->save();
        $user = User::find($request->input('id'));
        return response()->json($user, 200);
    }
    public function changePassWord(Request $request){

        //   $request->validate([
        //     'current_password' => 'required',
        //     'new_password' => 'required|min:8|confirmed',
        // ]);


        $user = User::find($request->id);
        Log::info($user);
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Mật khẩu hiện tại không đúng'], 400);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Mật khẩu đã được thay đổi thành công']);
    }
}
