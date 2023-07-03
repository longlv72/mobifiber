<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Closure;

class EmployeeImport implements ToCollection, WithStartRow
{
    public $row_import = [];

    public $countError = 0;

    public $row_errors_number = 0;

    public $username_array = [];
    public $email_array = [];
    public $phone_array = [];

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($row->filter()->isNotEmpty()) {
                // if ($index == 0) continue;
                $row['2'] = trim($row['2']);
                $row['3'] = trim($row['3']);
                $row['4'] = trim($row['4']);
                $rules = [
                    '0' => [ // tên
                        'required',
                    ],
                    '1' => [
                        'required', // họ
                    ],
                    '2' => [
                        'required',
                        Rule::unique('users', 'username')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->username_array)) {
                                $fail("tên đăng nhập(username) đã bị trùng");
                            }
                            array_push($this->username_array, $value);
                        },
                    ],
                    '3' => [ // email
                        'required',
                        "email:rfc,dns",
                        Rule::unique('users', 'email')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->email_array)) {
                                $fail("email đã bị trùng");
                            }
                            array_push($this->email_array, strtolower($value));
                        },
                    ],
                    '4' => [ // số điện thoại
                        'required',
                        Rule::unique('users', 'phone')->where('is_deleted', 0),
                        'digits_between:10,11',
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->phone_array)) {
                                $fail("số điện thoại đã bị trùng");
                            }
                            array_push($this->phone_array, $value);
                        },
                    ],
                    '5' => [// 5 Địa chỉ
	                    'required',
	                    Rule::in(['1', '2', '3'])
	                ],
                    // 5 Địa chỉ
                    '6' => [ // mật khẩu
                        "required",
                        "min:6"
                    ],
                    '7' => [ // loại tài khoản
                        "required",
                    ],
                ];

                $messages = [
                    '0.required'    => "thiếu tên nhân viên",
                    '1.required'    => "thiếu họ của nhân viên",
                    '2.required'    => "thiếu tên đăng nhập",
                    '2.unique'      => "tên đăng nhập đã tồn tại",
                    '3.required'    => "thiếu email",
                    '3.email'       => "email không đúng định dạng",
                    '3.unique'      => "email đã tồn tại",
                    '4.required'    => "thiếu số điện thoại liên hệ",
                    '4.unique'      => "số điện thoại liên hệ đã tồn tại",
                    '4.digits_between'     => "số điện thoại phải là số và có 10 hoặc 11 số",
                    '5.required'    => "Thiếu đơn vị thực quản lý",
                	'5.in'          => "Đơn vị thực quản lý không hợp lệ",
                    '6.required'    => "thiếu số tài khoản ngân hàng",
                    '6.min'         => "mật khẩu phải tối thiểu 6 kí tự",
                    '7.unique'      => "thiếu loại tài khoản",
                ];
                // Tạo validator với dữ liệu và rules
                $validator = Validator::make($row->toArray(), $rules, $messages);
                $listErrors = [];

                // Kiểm tra lỗi
                if ($validator->fails()) {
                    // Lấy danh sách các lỗi
                    $errors = $validator->errors()->messages();

                    // Lặp qua danh sách các lỗi và thêm cột error với tên lỗi vào dòng đó
                    foreach ($errors as $messages) {
                        foreach ($messages as $message) {
                            array_push($listErrors, $message);
                            $this->countError += 1;
                        }
                    }
                    $this->row_errors_number += 1;
                }
                $row_array_data = [
                    '0' => $row['0'],
                    '1' => $row['1'],
                    '2' => $row['2'],
                    '3' => $row['3'],
                    '4' => $row['4'],
                    '5' => $row['5'],
                    '6' => $row['6'],
                    '7' => $row['7'],
                ];
                $this->row_import[] = array_merge($row_array_data, ['error' => count($listErrors) > 0 ? implode(", ", $listErrors) : '']);
            }
        }
    }
}
