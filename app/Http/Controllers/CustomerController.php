<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExport;
use App\Http\Requests\CreateSingleCustomerRequest;
use App\Http\Requests\UpdateSingleCustomerRequest;
use App\Imports\CustomerImport;
use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends CoreController
{
    public $data = [];

    public function __construct()
    {
    }

    public function export(Request $request)
    {
        $type = (int) $request->customer_type_filter;
        $search_value =  mb_strtolower($request->search_filter);

        $data = Customer::where('is_deleted', 0)
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->where(function ($query) use ($search_value) {
                $query->when($search_value, function ($q) use ($search_value) {
                    $q->where(DB::raw("LOWER(CONCAT(CONCAT(last_name, ' '), firstname))"), 'LIKE', "%" . $search_value . "%")
                        ->orWhere(DB::raw("LOWER(phone)"), 'LIKE', "%" . $search_value . "%")
                        ->orWhere(DB::raw("LOWER(email)"), 'LIKE', "%" . $search_value . "%")
                        ->orWhere(DB::raw("LOWER(cccd)"), 'LIKE', "%" . $search_value . "%");
                });
            })
            ->orderBy('id', 'desc')->get();

        $params = ['data' => $data, 'template' => 'exports.customers'];
        $filename = 'customers_at_' . date('H_i_s_d_m_Y') . '.xlsx';

        return Excel::download(new CustomerExport($params), $filename);
    }

    public function view(Request $request)
    {
        $this->data['existing_customers'] = Contract::select('customer_id')->where('is_deleted', 0)->distinct()->get();
        $number_all_customer = Customer::select('id')->where('is_deleted', 0)->distinct()->get();
        $this->data['potential_customers'] = count($number_all_customer) - count($this->data['existing_customers']);
        $this->data['template_import'] = url('templates/TemplateCustomer.xlsx');
        return view('customers.list', $this->data);
    }

    public function create(CreateSingleCustomerRequest $request)
    {
        DB::beginTransaction();
        try {
            $data_customers = [
                "code"                  => $request->code,
                "firstname"             => $request->firstname,
                "last_name"             => $request->last_name,
                "cccd"                  => $request->cccd,
                "email"                 => $request->email,
                "username"              => $request->username,
                "phone"                 => $request->phone,
                "type"                  => $request->customer_type,
                "created_at"            => date('Y-m-d H:i:s'),
                "created_by"            => Auth::user()->id
            ];

            // handle upload cccd_front and cccd_backside
            if ($request->hasFile('cccd_front')) {
                $file = $request->file('cccd_front');
                $filename = $file->hashName();

                $check_put_file_to_disk = Storage::disk('cccd')->put($filename, file_get_contents($file));

                if ($check_put_file_to_disk) {
                    $data_customers = array_merge($data_customers, ['cccd_front' => $filename]);
                }
            }
            if ($request->hasFile('cccd_backside')) {
                $file = $request->file('cccd_backside');
                $filename = $file->hashName();

                $check_put_file_to_disk = Storage::disk('cccd')->put($filename, file_get_contents($file));

                if ($check_put_file_to_disk) {
                    $data_customers = array_merge($data_customers, ['cccd_backside' => $filename]);
                }
            }


            DB::table('customers')->insert($data_customers);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Thêm mới thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
        }
    }

    public function update(UpdateSingleCustomerRequest $request)
    {
        $customer_id = $request->id;
        $data_customer = [
            "code"                  => $request->code,
            "firstname"             => $request->firstname,
            "last_name"             => $request->last_name,
            "cccd"                  => $request->cccd,
            "email"                 => $request->email,
            "username"              => $request->username,
            "phone"                 => $request->phone,
            "type"                  => $request->customer_type,
            "updated_by"            => Auth::user()->id,
            "updated_at"            => date('Y-m-d H:i:s'),
        ];

        DB::beginTransaction();
        try {
            $customer_info = DB::table('customers')->where('id', $customer_id)->first();
            if ($request->hasFile('cccd_front')) {

                // handle if has cccd_front
                if ($customer_info->cccd_front) {
                    unlink(public_path('uploads/cccd/' . $customer_info->cccd_front));
                }
                $file = $request->file('cccd_front');
                $filename = $file->hashName();

                $check_put_file_to_disk = Storage::disk('cccd')->put($filename, file_get_contents($file));

                if ($check_put_file_to_disk) {
                    $data_customer = array_merge($data_customer, ['cccd_front' => $filename]);
                }
            }
            if ($request->hasFile('cccd_backside')) {

                // handle if has cccd_backside
                if ($customer_info->cccd_front) {
                    unlink(public_path('uploads/cccd/' . $customer_info->cccd_backside));
                }
                $file = $request->file('cccd_backside');
                $filename = $file->hashName();

                $check_put_file_to_disk = Storage::disk('cccd')->put($filename, file_get_contents($file));

                if ($check_put_file_to_disk) {
                    $data_customer = array_merge($data_customer, ['cccd_backside' => $filename]);
                }
            }

            DB::table('customers')->where('id', $customer_id)->update($data_customer);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Cập nhật thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server");
        }
    }

    public function delete(Request $request)
    {
        $table_name = 'customers';
        $id = $request->id;
        $device = Customer::where('id', $id)->first();

        if (!$device) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin khách hàng không tồn tại");
        }

        DB::beginTransaction();
        try {
            DB::table($table_name)->where('id', $id)->update(['is_deleted'   => 1]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Xóa thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }

    public function view_list(Request $request)
    {
        $limit = $request->limit != null ? $request->limit : 10; // page_size
        $offset = $request->offset != null ? $request->offset : 0;
        $type = (int) $request->type;
        $search_value =  mb_strtolower($request->search_value);

        $data = Customer::where('is_deleted', 0)
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->where(function ($query) use ($search_value) {
                $query->when($search_value, function ($q) use ($search_value) {
                    $q->where(DB::raw("LOWER(firstname)"), 'LIKE', "%" . $search_value . "%")
                        ->orWhere(DB::raw("LOWER(phone)"), 'LIKE', "%" . $search_value . "%")
                        ->orWhere(DB::raw("LOWER(email)"), 'LIKE', "%" . $search_value . "%")
                        ->orWhere(DB::raw("LOWER(cccd)"), 'LIKE', "%" . $search_value . "%");
                });
            })
            ->orderBy('id', 'desc');

        $count = $data->count();
        // $data_service = $data->offset($offset)->limit($limit)->get();
        $data_customers = $data->get();
        foreach ($data_customers as $key => $value) {
            if ($value->cccd_front != null || $value->cccd_backside != null) {
                $value->cccd_front_url = url('/uploads/cccd/' . $value->cccd_front);
                $value->cccd_backside_url = url('/uploads/cccd/' . $value->cccd_backside);
            }
        }
        return response()->json([
            "success"   => true,
            "message"   => "Lấy dữ liệu thành công",
            'rows'      => $data_customers,
            'total'     => $count,
        ], 200);
    }

    public function import(Request $request)
    {
        try {
            $import = new CustomerImport();
            $file = $request->file('fileExcel');
            Excel::import($import, $file);

            // Lấy mảng các hàng lỗi
            $rows = $import->row_import;

            // Trả lại mảng các hàng lỗi cho client
            // return response()->json(['errors' => $errors]);
            return $this->responseJSONSuccess($this::IS_NOT_COMMIT_DB, "Ok", [
                'rows'          => $rows,
                'total'         => count($rows),
                'totalError'    => $import->countError,
                'row_errors'    => $import->row_errors_number,
            ]);
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_NOT_ROLL_BACK_DB, "Lỗi tệp tin. Vui lòng tải lại tệp tin để thử lại");
            // return $this->responseJSONFalse($this::IS_NOT_ROLL_BACK_DB, $th->getMessage() . ' - line: ' . $th->getLine() . ' - file: ' . $th->getFile());
        }
    }

    public function update_data(Request $request)
    {
        $data = json_decode($request->data, true);
        if (!$data || count($data) <= 0) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Không có dữ liệu để import");
        }
        $data_arr = [];
        foreach ($data as $item) {
            $firstNameToAscii = $this->unicode_to_ascii($item['0']);
            $words = explode(" ", $firstNameToAscii);
            $acronym = "";
            foreach ($words as $w) {
                $acronym .= mb_substr($w, 0, 1);
            }
            $data_arr[] = [
                "code"                  => $acronym . '_' . rand(10, 100),
                "firstname"             => $item['0'],
                "cccd"                  => $item['1'],
                "email"                 => $item['2'],
                "phone"                 => $item['3'],
                "type"                  => $item['4'],
                "username"              => $item['5'],
                "is_deleted"            => 0,
                "created_at"            => date('Y-m-d H:i:s'),
                "created_by"            => Auth::user()->id
            ];
        }

        DB::beginTransaction();
        try {
            DB::table('customers')->insert($data_arr);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Import dữ liệu thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }
    private function unicode_to_ascii($string)
    {
        $needtobt = array(
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Ă' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Æ' => 'A', 'Ǽ' => 'A',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'ă' => 'a', 'ā' => 'a', 'ą' => 'a', 'æ' => 'a', 'ǽ' => 'a',
            'Þ' => 'B', 'þ' => 'b', 'ß' => 'Ss',
            'Ç' => 'C', 'Č' => 'C', 'Ć' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C',
            'ç' => 'c', 'č' => 'c', 'ć' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
            'Đ' => 'Dj', 'Ď' => 'D', 'Đ' => 'D',
            'đ' => 'dj', 'ď' => 'd',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ĕ' => 'E', 'Ē' => 'E', 'Ę' => 'E', 'Ė' => 'E',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ĕ' => 'e', 'ē' => 'e', 'ę' => 'e', 'ė' => 'e',
            'Ĝ' => 'G', 'Ğ' => 'G', 'Ġ' => 'G', 'Ģ' => 'G',
            'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g',
            'Ĥ' => 'H', 'Ħ' => 'H',
            'ĥ' => 'h', 'ħ' => 'h',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'İ' => 'I', 'Ĩ' => 'I', 'Ī' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'į' => 'i', 'ĩ' => 'i', 'ī' => 'i', 'ĭ' => 'i', 'ı' => 'i',
            'Ĵ' => 'J',
            'ĵ' => 'j',
            'Ķ' => 'K',
            'ķ' => 'k', 'ĸ' => 'k',
            'Ĺ' => 'L', 'Ļ' => 'L', 'Ľ' => 'L', 'Ŀ' => 'L', 'Ł' => 'L',
            'ĺ' => 'l', 'ļ' => 'l', 'ľ' => 'l', 'ŀ' => 'l', 'ł' => 'l',
            'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N', 'Ņ' => 'N', 'Ŋ' => 'N',
            'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŋ' => 'n', 'ŉ' => 'n',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ō' => 'O', 'Ŏ' => 'O', 'Ő' => 'O', 'Œ' => 'O',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ō' => 'o', 'ŏ' => 'o', 'ő' => 'o', 'œ' => 'o', 'ð' => 'o',
            'Ŕ' => 'R', 'Ř' => 'R',
            'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r',
            'Š' => 'S', 'Ŝ' => 'S', 'Ś' => 'S', 'Ş' => 'S',
            'š' => 's', 'ŝ' => 's', 'ś' => 's', 'ş' => 's',
            'Ŧ' => 'T', 'Ţ' => 'T', 'Ť' => 'T',
            'ŧ' => 't', 'ţ' => 't', 'ť' => 't',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ũ' => 'U', 'Ū' => 'U', 'Ŭ' => 'U', 'Ů' => 'U', 'Ű' => 'U', 'Ų' => 'U',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ũ' => 'u', 'ū' => 'u', 'ŭ' => 'u', 'ů' => 'u', 'ű' => 'u', 'ų' => 'u',
            'Ŵ' => 'W', 'Ẁ' => 'W', 'Ẃ' => 'W', 'Ẅ' => 'W',
            'ŵ' => 'w', 'ẁ' => 'w', 'ẃ' => 'w', 'ẅ' => 'w',
            'Ý' => 'Y', 'Ÿ' => 'Y', 'Ŷ' => 'Y',
            'ý' => 'y', 'ÿ' => 'y', 'ŷ' => 'y',
            'Ž' => 'Z', 'Ź' => 'Z', 'Ż' => 'Z', 'Ž' => 'Z',
            'ž' => 'z', 'ź' => 'z', 'ż' => 'z', 'ž' => 'z',
            '“' => '"', '”' => '"', '‘' => "'", '’' => "'", '•' => '-', '…' => '...', '—' => '-', '–' => '-', '¿' => '?', '¡' => '!', '°' => ' degrees ',
            '¼' => ' 1/4 ', '½' => ' 1/2 ', '¾' => ' 3/4 ', '⅓' => ' 1/3 ', '⅔' => ' 2/3 ', '⅛' => ' 1/8 ', '⅜' => ' 3/8 ', '⅝' => ' 5/8 ', '⅞' => ' 7/8 ',
            '÷' => ' divided by ', '×' => ' times ', '±' => ' plus-minus ', '√' => ' square root ', '∞' => ' infinity ',
            '≈' => ' almost equal to ', '≠' => ' not equal to ', '≡' => ' identical to ', '≤' => ' less than or equal to ', '≥' => ' greater than or equal to ',
            '←' => ' left ', '→' => ' right ', '↑' => ' up ', '↓' => ' down ', '↔' => ' left and right ', '↕' => ' up and down ',
            '℅' => ' care of ', '℮' => ' estimated ',
            'Ω' => ' ohm ',
            '♀' => ' female ', '♂' => ' male ',
            '©' => ' Copyright ', '®' => ' Registered ', '™' => ' Trademark ',
        );
        $string = strtr($string, $needtobt);
        $string = preg_replace("/[^\x9\xA\xD\x20-\x7F]/u", "", $string);
        return $string;
    }
}
