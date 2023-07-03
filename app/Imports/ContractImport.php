<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Closure;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ContractImport implements ToCollection, WithStartRow
{
    public $row_import = [];

    public $countError = 0;

    public $row_errors_number = 0;

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    // public function columnFormats() : array {
    //     return [
    //         '1' => NumberFormat::FORMAT_DATE_YYYYMMDD
    //     ];
    // }

    // public function mapping(): array
    // {
    //     return [
    //         '1'  => '1',
    //     ];
    // }

    // public function transformDate($value, $format='Y-m-d') {
    //     try {
    //         if (!$value) {
    //             return null;
    //         }
    //         return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format($format));
    //     } catch (\Throwable $th) {
    //         return $value;
    //     }
    // }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($row->filter()->isNotEmpty()) {
                $row['2'] = is_int($row['2']) ? Date::excelToDateTimeObject($row['2'])->format('Y-m-d H:i:s') : '';
                $row['5'] = is_int($row['5']) ? Date::excelToDateTimeObject($row['5'])->format('Y-m-d H:i:s') : '';
                $row['1'] = trim($row['1']);
                $row['3'] = trim($row['3']);
                $row['7'] = trim($row['7']);
                $row['8'] = trim($row['8']);

                $rules = [
                    '0' => [ // mã hợp đồng
                        'required',
                    ],
                    '1' => [ // mã gói cước
                        'required',
                        Rule::exists('packages', 'package_code')->where('is_deleted', 0)
                    ],
                    '2' => [ // Ngày bắt đầu gói cước
                        'required',
                        'date_format:Y-m-d H:i:s'
                    ],
                    '3' => [ // mã thiết bị
                        'nullable',
                        Rule::exists('devices', 'device_code')->where('is_deleted', 0)
                    ],
                    '4' => [ // mã hóa đơn
                        'nullable',
                    ],
                    '5' => [ // ngày tạo hóa đơn
                        "nullable",
                        'date_format:Y-m-d H:i:s'
                    ],
                    '6' => [ // mã đối tác hạ tầng
                        "required",
                        Rule::exists('partners', 'partner_code')->where('is_deleted', 0)->where('is_active', 1)
                    ],
                    '7' => [ // mã tòa nhà
                        "required",
                        Rule::exists('buildings', 'building_code')->where('is_deleted', 0)
                    ],
                    '8' => [ // mã ID khách hàng
                        "required",
                        Rule::exists('customers', 'code')->where('is_deleted', 0)
                    ],
                    '9' => [ // mã khách hàng tự sinh, mã KH theo hợp đồng, tự điền và lưu trong bảng hợp đồng (contracts)
                        "required",
                    ],
                    '10' => [ // loại hình hợp tác
                        "required",
                        Rule::in(['1', '2', '3'])
                    ],
                    '11' => [ // mã cửa hàng, shopcode
                        "nullable",
                    ],
                    '12' => [ // username x viên từ bảng nhân users. ở đây là ng tạo hợp đồng
                        "required",
                        Rule::exists('users', 'username')->where('is_deleted', 0)
                    ],
                    '13' => [ // trạng thái hợp đồng
                        "required",
                        Rule::in(['1', '2', '3'])
                    ],
                ];

                $messages = [
                    '0.required'        => "thiếu mã hợp đồng",
                    '1.required'        => "thiếu mã gói cước",
                    '1.exists'          => "mã gói cước không tồn tại",
                    '2.required'        => "thiếu ngày bắt đầu gói cước",
                    '2.date_format'     => "ngày bắt đầu gói cước không đúng định dạng",
                    '3.required'        => "thiếu mã thiết bị",
                    '3.exists'          => "mã thiết bị không tồn tại",
                    '4.required'        => "thiếu mã hóa đơn",
                    '5.required'        => "thiếu ngày tạo hóa đơn",
                    '5.date_format'     => "ngày tạo hóa đơn không đúng định dạng",
                    '6.required'        => "thiếu mã đối tác hạ tầng",
                    '6.exists'          => "mã đối tác hạ tầng không tồn tại hoặc đã bị vô hiệu hóa",
                    '7.required'        => "thiếu mã tòa nhà",
                    '7.exists'          => "mã tòa nhà không tồn tại",
                    '8.required'        => "thiếu mã code khách hàng",
                    '8.exists'          => "mã code khách hàng không tồn tại",
                    '9.required'       => "thiếu mã ID khách hàng theo hợp đồng",
                    '10.in'             => "Loại hình hợp tác không hợp lệ",
                    '10.required'       => "Thiếu loại hình hợp tác",
                    '12.required'       => "Thiếu mã nhân viên (username)",
                    '12.exists'         => "Mã nhân viên (username) không tồn tại",
                    '13.required'       => "Thiếu trạng thái hợp đồng",
                    '13.in'             => "trạng thái hợp đồng không hợp lệ",
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
                    '11' => $row['11'],
                    '12' => $row['12'],
                    '13' => $row['13'],
                    '14' => $row['14'],
                    '15' => $row['15'],
                ];
                $this->row_import[] = array_merge($row_array_data, ['error' => count($listErrors) > 0 ? implode(", ", $listErrors) : '']);
            }
        }
    }
}
