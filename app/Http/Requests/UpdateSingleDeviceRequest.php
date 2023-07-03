<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSingleDeviceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "device_code"             => [
                "required",
                Rule::unique('devices', 'device_code')->ignore($this->id)->where('is_deleted', 0)
            ],
            "device_name"             => "required",
            "serial_number"           => "required",
            "device_status"           => "required",
        ];
    }

    public function messages() {
        return [
            "device_code.required"           => "Thiếu mã thiết bị",
            "device_code.unique"             => "Mã thiết bị đã tồn tại",
            "device_name.required"           => "Thiếu tên thiết bị",
            "serial_number.required"         => "Thiếu serial thiết bị",
            "device_status.required"         => "Thiếu trạng thái thiết bị",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 200)
        );
    }
}
