<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends CoreController
{
    public $data = [];

    public function __construct(){

    }

    public function saveProfile(UpdateProfileRequest $request) {
        $user_id = Auth::user()->id;

        $data_user_profile = [
            'phone'     => $request->phone,
            'address'   => $request->address,
            'email'     => $request->email,
            'lastname'  => $request->last_name,
            'firstname' => $request->first_name
        ];

        DB::beginTransaction();
        try {
            DB::table('users')->where('id', $user_id)->update($data_user_profile);
            return $this->responseJSONSuccessWithData($this::IS_COMMIT_DB, "Đã cập nhật thông tin", ['data' => $data_user_profile]);
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server. Vui lòng thử lại sau!");
        }
    }

    public function changePass(ChangePasswordRequest $request) {
        DB::beginTransaction();
        try {
            $new_pass = $request->new_password;
            DB::table('users')->where('id', Auth::user()->id)->update(['password' => Hash::make($new_pass)]);

            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Đổi mật khẩu thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server. Vui lòng thử lại sau!");
        }
    }
}
