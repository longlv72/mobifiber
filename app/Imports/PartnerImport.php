<?php

namespace App\Imports;

use App\Models\Partner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Closure;

class PartnerImport implements ToCollection, WithStartRow
{

    public $partner_row_errors = [];

    public $countError = 0;

    public $row_errors = 0;

    public $email_array = [];
    public $phone_array = [];
    public $partner_code_array = [];
    public $business_license_array = [];
    public $number_bank_array = [];
    public $contact_phone_array = [];

    // use when implement WithHeadingRow interface
    public function headingRow(): int
    {
        return 1;
    }


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
                $row['1'] = trim($row['1']);
                $row['2'] = trim($row['2']);
                $row['4'] = trim($row['4']);
                $row['5'] = trim($row['5']);
                $row['6'] = trim($row['6']);
                $row['9'] = trim($row['9']);
                $rules = [
                    '0' => [ // tên đối tác
                        'required',
                    ],
                    '1' => [ // email
                        'required',
                        "email:rfc,dns",
                        Rule::unique('partners', 'email')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->email_array)) {
                                $fail("email đã bị trùng");
                            }
                            array_push($this->email_array, strtolower($value));
                        },
                    ],
                    '2' => [ // số điện thoại
                        'required',
                        Rule::unique('partners', 'phone')->where('is_deleted', 0),
                        'digits_between:10,11',
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->phone_array)) {
                                $fail("số điện thoại đối tác đã bị trùng");
                            }
                            array_push($this->phone_array, $value);
                        },
                    ],
                    '3' => [ // địa chỉ
                        'nullable'
                    ],
                    '4' => [ // mã code đối tác
                        'required',
                        Rule::unique('partners', 'partner_code')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->partner_code_array)) {
                                $fail("mã code đối tác đã bị trùng");
                            }
                            array_push($this->partner_code_array, $value);
                        },
                    ],
                    '5' => [ // giấy phép kinh doanh
                        "required",
                        Rule::unique('partners', 'business_license')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->business_license_array)) {
                                $fail("giấy phép kinh doanh đã bị trùng");
                            }
                            array_push($this->business_license_array, $value);
                        },
                    ],
                    '6' => [ // stk bank
                        "required",
                        Rule::unique('partners', 'number_bank')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->number_bank_array)) {
                                $fail("số tài khoản ngân hàng đã bị trùng");
                            }
                            array_push($this->number_bank_array, $value);
                        },
                    ],
                    '7' => [ // Tên ngân hàng
                        "required",
                    ],
                    '8' => [ // Đầu mối
                        "nullable",
                    ],
                    '9' => [ // số điện thoại đầu mối
                        'digits_between:10,11',
                        Rule::unique('partners', 'contact_phone')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->contact_phone_array)) {
                                $fail("số điện thoại đối tác đã bị trùng");
                            }
                            array_push($this->contact_phone_array, $value);
                        },
                    ],
                    '10' => [ // Loại hình hợp tác
                        "required",
                        Rule::in(['1', '2', '3'])
                    ],
                ];

                $messages = [
                    '0.required'    => "thiếu tên đối tác",
                    '1.required'    => "thiếu thông tin email",
                    '1.unique'      => "email đã tồn tại",
                    '1.email'       => "email không đúng định dạng",
                    '2.required'    => "thiếu số điện thoại",
                    '2.digits_between'      => "số điện thoại phải là số và có 10 hoặc 11 ký tự",
                    '2.unique'      => "số điện thoại đã tồn tại",
                    '4.required'    => "thiếu mã đối tác",
                    '4.unique'      => "mã đối tác đã tồn tại",
                    '5.required'    => "thiếu giấy phép kinh doanh",
                    '5.unique'      => "giấy phép kinh doanh đã tồn tại",
                    '6.required'    => "thiếu số tài khoản ngân hàng",
                    '6.unique'      => "số tài khoản ngân hàng đã tồn tại",
                    '7.required'      => "thiếu tên ngân hàng",
                    '9.unique'      => "số điện thoại đầu mối đã tồn tại",
                    '9.digits_between'      => "số điện thoại đầu mối phải là số và 10 hoặc 11 ký tự",
                    '10.required'   => "thiếu loại hình hợp tác",
                    '10.in'         => "loại hình hợp tác không hợp lệ",
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
                    $this->row_errors += 1;
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
                    '8' => $row['8'],
                    '9' => $row['9'],
                    '10' => $row['10'],
                ];
                $this->partner_row_errors[] = array_merge($row_array_data, ['error' => count($listErrors) > 0 ? implode(", ", $listErrors) : '']);
            }
        }
    }
}
