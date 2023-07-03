<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Closure;

class DeviceImport implements ToCollection, WithStartRow
{
    public $row_import = [];

    public $countError = 0;

    public $row_errors_number = 0;

    public $code_array = [];
    public $serial_array = [];

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
                // dd($row);
                $row['0'] = trim($row['0']);
                $row['2'] = trim($row['2']);
                $rules = [
                    '0' => [ // tên gói cước
                        'required',
                        Rule::unique('devices', 'device_code')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->code_array)) {
                                $fail("mã thiết bị đã bị trùng");
                            }
                            array_push($this->code_array, $value);
                        },
                    ],
                    '1' => [
                        'required', // tên thiết bị
                    ],
                    '2' => [ // số serial
                        'required',
                        Rule::unique('devices', 'serial')->where('is_deleted', 0),
                        function (string $attribute, mixed $value, Closure $fail) {
                            if (in_array($value, $this->serial_array)) {
                                $fail("số serial đã bị trùng");
                            }
                            array_push($this->serial_array, $value);
                        },
                    ],
                ];

                $messages = [
                    '0.required'    => "thiếu mã thiết bị",
                    '0.unique'      => "mã thiết bị đã tồn tại",
                    '1.required'    => "thiếu tên thiết bị",
                    '2.required'    => "thiếu số serial",
                    '2.unique'      => "số serial đã tồn tại",
                    // '3.required'    => "thiếu trạng thái",
                    // '3.in'          => "trạng thái thiết bị không hợp lệ",
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
                        // dd(
                        foreach ($messages as $message) {
                            array_push($listErrors, $message);
                            $this->countError += 1;
                        }
                    }
                    $this->row_errors_number += 1;
                }
                $this->row_import[] = array_merge([
                    '0' => $row['0'],
                    '1' => $row['1'],
                    '2' => $row['2'],
                ], ['error' => count($listErrors) > 0 ? implode(", ", $listErrors) : '']);
            }
        }
    }
}
