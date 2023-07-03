<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Closure;

class CustomerImport implements ToCollection, WithStartRow
{
    public $row_import = [];

    public $countError = 0;

    public $row_errors_number = 0;

    public $username_array = [];
    public $phone_array = [];
    public $partner_code_array = [];

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
                $row['0'] = trim($row['0']);
                $row['5'] = trim($row['5']);
                $rules = [
                    '0' => [
                        'required', // họ và tên đệm khách hàng
                    ],
                    '1' => [ // cccd/cmt
                        'required',
                    ],
                    '2' => [ // email
                        "required",
                        "email:rfc,dns",
                    ],
                    '3' => [ // số điện thoại
                        'required',
                        Rule::unique('customers', 'phone')->where('is_deleted', 0),
                        "digits_between:10,11",
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->phone_array)) {
                                $fail("số điện thoại đã bị trùng");
                            }
                            array_push($this->phone_array, $value);
                        },
                    ],
                    '4' => [ // loại khách hàng
                        "required",
                        Rule::in(['1', '2'])
                    ],
                    '5' => [ // tài khoản KH
                        'required',
                        Rule::unique('customers', 'username')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->username_array)) {
                                $fail("tài khoản khách hàng đã bị trùng");
                            }
                            array_push($this->username_array, $value);
                        },
                    ],
                ];

                $messages = [
                    '0.required'        => "thiếu họ và tên khách hàng",
                    '1.required'        => "thiếu CCCD/CMT khách hàng",
                    '2.required'        => "thiếu email khách hàng",
                    '2.email'           => "email khách hàng không đúng định dạng",
                    '3.unique'          => "SDT khách hàng đã tồn tại",
                    '3.required'        => "thiếu số điện thoại khách hàng",
                    '3.digits_between'  => "số điện thoại đầu mối phải là số và có 10 hoặc 11 số",
                    '4.required'        => "thiếu loại khách hàng",
                    '4.in'              => "loại khách hàng không hợp lệ",
                    '5.required'        => "thiếu tài khoản khách hàng",
                    '5.unique'          => "tài khoản khách hàng đã tồn tại",
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
                    '5' => $row['5']
                ];
                $this->row_import[] = array_merge($row_array_data, ['error' => count($listErrors) > 0 ? implode(", ", $listErrors) : '']);
            }
        }
    }
}
