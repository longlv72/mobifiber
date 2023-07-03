<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Closure;

class PackageImport implements ToCollection, WithStartRow
{
    public $row_import = [];

    public $countError = 0;

    public $row_errors_number = 0;

    public $code_array = [];
    public $decision_array = [];

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
                $row['6'] = trim($row['6']);
                $rules = [
                    '0' => [ // tên gói cước
                        'required',
                    ],
                    '1' => [
                        'required', // mã gói cước
                        Rule::unique('packages', 'package_code')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->code_array)) {
                                $fail("mã gói cước đã bị trùng");
                            }
                            array_push($this->code_array, $value);
                        },
                    ],
                    // giá trước thuế
                    '2' => [
                        'required',
                        'numeric',
                    ],
                    // giá sau thuế
                    '3' => [
                        'required',
                        'numeric',
                    ],
                    '4' => [ // thời gian sử dụng
                        'required',
                        'numeric',
                        'min:0'
                    ],
                    '5' => [ // khuyến mại
                        'numeric',
                        'min:0'
                    ],
                    '6' => [ // quyết định số
                        "required",
                        Rule::unique('packages', 'decision')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->decision_array)) {
                                $fail("số quyết định đã bị trùng");
                            }
                            array_push($this->decision_array, $value);
                        },
                    ],
                ];

                $messages = [
                    '0.required'    => "thiếu tên gói cước",
                    '1.required'    => "thiếu mã gói cước",
                    '1.unique'      => "mã gói cước đã tồn tại",
                    '2.required'    => "thiếu giá trước thuế",
                    '2.numeric'     => "giá trước thuế phải là số",
                    '3.required'    => "thiếu giá sau thuế",
                    '3.numeric'     => "giá sau thuế phải là số",
                    '4.required'    => "thiếu thời gian sử dụng (tháng)",
                    '4.numeric'     => "thời gian sử dụng (tháng) phải là số và lớn hơn 0",
                    '4.min'         => "thời gian sử dụng (tháng) phải là số và lớn hơn 0",
                    '5.numeric'     => "khuyến mại (tháng) phải là số và lớn hơn 0",
                    '5.min'         => "khuyến mại (tháng) phải là số và lớn hơn 0",
                    '6.required'    => "thiếu số quyết định của gói cước",
                    '6.unique'      => "số quyết định đã tồn tại",
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
                ];
                $this->row_import[] = array_merge($row_array_data, ['error' => count($listErrors) > 0 ? implode(", ", $listErrors) : '']);
            }
        }
    }
}
