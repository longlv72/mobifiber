<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Closure;

class BuildingImport implements ToCollection, WithStartRow
{
    public $row_import = [];

    public $countError = 0;

    public $row_errors_number = 0;

    public $code_array = [];
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
                $row['8'] = trim($row['8']);
                $row['7'] = trim($row['7']);
                $row['0'] = trim($row['0']);
                $rules = [
                    '0' => [ // mã tòa nhà
                        'required',
                        Rule::unique('buildings', 'building_code')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->code_array)) {
                                $fail("mã tòa nhà đã bị trùng");
                            }
                            array_push($this->code_array, $value);
                        },
                    ],
                    '1' => [
                        'required', // tên tòa nhà
                    ],
                    '2' => [ // chủ sở hữu
                        '',
                    ],
                    '3' => [ // địa chỉ
                        'required',
                    ],
                    '4' => [ // kinh độ
                        'required',
                    ],
                    '5' => [ // vĩ độ
                        'required',
                    ],
                    '6' => [ // đầu mối tòa nhà
                        "nullable",
                    ],
                    '7' => [
                        "nullable", // số điện thoại đầu mối
                        "digits_between:10,11",
                        // function (string $attribute, mixed $value, Closure $fail) {
                        //     if (in_array($value, $this->phone_array)) {
                        //         $fail("số điện thoại đã bị trùng");
                        //     }
                        //     array_push($this->phone_array, $value);
                        // },
                    ],
                    '8' => [ // mã đối tác
                        'required',
                        Rule::exists('partners', 'partner_code')->where('is_deleted', 0),
                    ],
                    '9' => [ // Loại hình hợp tác
                        "required",
                        Rule::in(['1', '2', '3'])
                    ],
                    '10' => [ // Tỉ lệ chia sẻ
                        "required",
                        "numeric",
                        "min:0",
                        "max:100"
                    ],
                ];

                $messages = [
                    '0.required'    => "thiếu mã tòa nhà",
                    '0.unique'      => "mã tòa nhà đã tồn tại",
                    '1.required'    => "thiếu tên tòa nhà",
                    '3.required'    => "thiếu địa chỉ tòa nhà",
                    '4.required'    => "thiếu kinh độ",
                    '5.required'    => "thiếu vĩ độ",
                    '6.required'    => "thiếu đầu mối tòa nhà",
                    '7.digits_between' => "số điện thoại đầu mối phải có 10 hoặc số",
                    '8.required'    => "thiếu mã đối tác",
                    '8.exists'      => "Mã đối tác không tồn tại",
                    '8.unique'      => "mã đối tác đã tồn tại",
                    '9.required'    => "thiếu loại hình hợp tác",
                    '9.in'          => "loại hình hợp tác không hợp lệ",
                    '10.required'   => "thiếu tỉ lệ chia sẻ",
                    '10.numeric'    => "tỉ lệ chia sẻ phải là số",
                    '10.min'        => "tỉ lệ chia sẻ nhỏ nhất là 0",
                    '10.max'        => "tỉ lệ chia sẻ lớn nhất là 100",
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
                    '8' => $row['8'],
                    '9' => $row['9'],
                    '10' => $row['10'],
                ];
                $this->row_import[] = array_merge($row_array_data, ['error' => count($listErrors) > 0 ? implode(", ", $listErrors) : '']);
            }
        }
    }
}
