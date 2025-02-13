<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{


    /**
     * Hàm lấy danh sách người dùng
     * @param
     * @return $users
     * CreatedBy: youngbachhh (31/03/2024)
     */
    public function index(Request $request)
    {
        // Lấy tham số từ request
        Log::info($request->all());
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);
        $priority = $request->input('priority_status', 'all');
        $searchConditions = $request->input('searchConditions');
        $searchedColumn = $request->input('searchedColumn');



        // Generate a unique cache key based on request parameters
        $cacheKey = 'users:' . md5(serialize($request->all()));

        // Kiểm tra cache trước
        $cachedUsers = Redis::get($cacheKey);
        if ($cachedUsers) {
            return response()->json(json_decode($cachedUsers), 200);
        }

        // Khởi tạo truy vấn
        $query = User::withCount('post')
            ->where('role_id', '!=', 7)
            ->whereNotIn('is_active', [2, 3]);

        // Xử lý điều kiện tìm kiếm
        if (!empty($searchConditions)) {
            $query->where(function ($q) use ($searchedColumn, $searchConditions) {

                    if ($searchedColumn === 'name') {
                        $q->orWhere('name', 'LIKE', '%' . $searchConditions . '%');
                    } elseif (in_array($searchedColumn, ['email', 'phone'])) {
                        $q->orWhere($searchedColumn, 'LIKE', '%' . $searchConditions . '%');
                    }
            });
        }

        // Áp dụng các bộ lọc (ví dụ: lọc theo trạng thái ưu tiên, nếu có)
        if ($priority !== 'all') {
            $query->where('priority_status', $priority);
        }

        // Sắp xếp theo cột và thứ tự (nếu có)
        $sortBy = $request->input('sortBy', 'updated_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Phân trang
        $users = $query->paginate($pageSize, ['*'], 'page', $page);

        // Cache kết quả cho 10 phút
        Redis::setex($cacheKey, 600, $users->toJson());

        return response()->json($users, 200);
    }



    public function approval(Request $request)
    {
        // return User::withCount('post')->get();
        $pageSize = $request->input('pageSize', 10);
        $users = User::withCount('post')->where('role_id', '!=', 7)->where('is_active', '=', 2)->orderBy('updated_at', 'desc')->paginate($pageSize);
        return response()->json($users);
    }

    public function approvalupdate(Request $request, $id)
    {
        $user = User::find($id);
        $user->is_active = $request->is_active;
        $user->save();
        return response()->json(['message' => 'Success'], 200);
    }

    public function collaborator(Request $request)
    {
        // return User::withCount('post')->get();
        $userId = $request->input('user_id');
        $pageSize = $request->input('pageSize', 10);
        Log::info($userId);
        $users = User::where('user_id', $userId)->whereIn('role_id', [7, 8])->orderBy('updated_at', 'desc')->paginate($pageSize);

        return response()->json($users);
    }

    public function userrole(Request $request)
    {

        // return User::withCount('post')
        // ->whereNotIn('role_id', [1, 6])
        // ->get();
        $pageSize = $request->input('pageSize', 10);
        $users = User::withCount('post')->whereNotIn('role_id', [1, 6, 7])->orderBy('updated_at', 'desc')->paginate($pageSize);
        return response()->json($users);
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
        Log::info($request->all());

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

        $timestamp = now()->format('YmdHis');
        $cccd_trc_path = null; // Khởi tạo biến đường dẫn cho cccd_trc
        $cccd_sau_path = null; // Khởi tạo biến đường dẫn cho cccd_sau

        // Xử lý file cccd_trc
        if ($request->hasFile('cccd_trc')) {
            $file = $request->file('cccd_trc');

            // Lưu ảnh và nhận đường dẫn
            $cccd_trc_path = $this->storeImage($file, $timestamp);
            Log::info($cccd_trc_path);
        }

        // Xử lý file cccd_sau
        if ($request->hasFile('cccd_sau')) {
            $file = $request->file('cccd_sau');

            // Lưu ảnh và nhận đường dẫn
            $cccd_sau_path = $this->storeImage($file, $timestamp);
            Log::info($cccd_sau_path);
        }

        // Tạo người dùng mới
        $user = User::create(
            [
                'name' => $request['name'],
                'email' => $request['email'],
                'cccd' => $request['cccd'],
                'birthday' => $request['birthday'],
                'phone' => $request['phone'],
                'address' => $request['address'],
                'gender' => $request['gender'],
                'workunit' => $request['workunit'],
                'role_id' => ($request['role_id'] == null ? 3 : (int)$request['role_id']),
                'is_active' => ($request['is_active'] == null ? 1 : (int)$request['is_active']),
                'password' => Hash::make($request['password']),
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => $request['user_id'],
                'cccd_trc' => $cccd_trc_path, // Lưu đường dẫn URL hoàn chỉnh của ảnh trước
                'cccd_sau' => $cccd_sau_path, // Lưu đường dẫn URL hoàn chỉnh của ảnh sau
            ]
        );

        // Nếu không tạo được người dùng, trả về thông báo
        if (!$user) {
            return response()->json(['message' => 'Thêm mới thất bại'], 400);
        }

        // Tạo permission cho người dùng
        Permission::create(
            [
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'access_permission_1' => $request['access_permission_1'],
                'access_permission_2' => $request['access_permission_2'],
                'access_permission_3' => $request['access_permission_3'],
                'access_permission_4' => $request['access_permission_4'],
                'access_permission_5' => $request['access_permission_5'],
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
        Log::info(User::findOrFail($id));
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
    public function update($id, Request $request)
    {
        Log::info($id);
        Log::info($request->all());

        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable'
        ], [
            'name.required' => 'Không được bỏ trống tên',
            'email.required' => 'Không được bỏ trống email',
            'email.email' => 'Email không đúng định dạng'
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Người dùng không tồn tại'], 404);
        }

        $password = $request->input('password');
        $userData = [
            "name" => $request->name,
            "email" => $request->email,
            "role_id" => $request->role_id,
            "is_active" => $request->is_active,
            'cccd' => $request->cccd,
            'phone' => $request->phone,
            'address' => $request->address,
            'workunit' => $request->workunit,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            "updated_at" => now(),
        ];

        if ($password != 'undefined') {
            $userData['password'] = Hash::make($password);
        }

        $timestamp = now()->format('YmdHis');
        if ($request->hasFile('cccd_trc')) {
            $file = $request->file('cccd_trc');
            $cccd_trc_path = $this->storeImage($file, $timestamp);
            $userData['cccd_trc'] = $cccd_trc_path;
            Log::info($cccd_trc_path);
        }

        if ($request->hasFile('cccd_sau')) {
            $file = $request->file('cccd_sau');
            $cccd_sau_path = $this->storeImage($file, $timestamp);
            $userData['cccd_sau'] = $cccd_sau_path;
            Log::info($cccd_sau_path);
        }

        $result = $user->update($userData);
        if (!$result) {
            return response()->json(['message' => 'Cập nhật user không thành công '], 401);
        }

        $permission = Permission::where('user_id', $id)->first();
        if ($permission) {
            $permission->update([
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'access_permission_1' => $request->access_permission_1,
                'access_permission_2' => $request->access_permission_2,
                'access_permission_3' => $request->access_permission_3,
                'access_permission_4' => $request->access_permission_4,
                'access_permission_5' => $request->access_permission_5,
            ]);
        }

        return response()->json(['message' => 'Cập nhật thành công'], 200);
    }

    private function storeImage($file, $timestamp)
    {
        $mimeType = $file->getMimeType();
        $isOctetStream = $mimeType == 'application/octet-stream';

        // Nếu là 'application/octet-stream' hoặc HEIC, chuyển sang JPEG
        if ($isOctetStream || strpos($mimeType, 'heic') !== false) {
            $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
            $path = 'images/cccd/' . uniqid() . '.jpg';
            $fullPath = public_path('storage/' . $path);

            imagejpeg($image, $fullPath, 85); // Chất lượng 85 cho nén ảnh JPEG
            imagedestroy($image); // Dọn dẹp bộ nhớ

            return url('storage/' . $path) . '?t=' . $timestamp;
        } else {
            // Lưu ảnh nếu MIME type hợp lệ
            $path = $file->store('images/cccd', 'public');
            return url('storage/' . $path) . '?t=' . $timestamp;
        }
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
        Log::info($user);

        // Xóa bản ghi trong bảng Permission
        $permissions = Permission::where('user_id', $id)->first();
        if ($permissions) {
            $permissions->delete();
        }

        // Xóa các bản ghi liên quan đến user trong bảng User (các cộng tác viên)
        $user_ctv = User::where('user_id', $id)->get();
        foreach ($user_ctv as $ctv) {
            $ctv->delete();
        }

        // Xóa bản ghi người dùng chính
        if ($user) {
            $user->delete();
        }

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
    public function changePassWord(Request $request)
    {

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
