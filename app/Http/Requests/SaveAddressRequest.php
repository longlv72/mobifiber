<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SaveAddressRequest extends FormRequest
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
            "id"            => [
                ""
            ],
            "address"  => [
                "required",
            ],
            "longitude" => [
                "nullable",
            ],
            "latitude" => [
                "nullable",
            ],
        ];
    }

    public function messages()
    {
        return [
            // 'id.required'                  => "Thiếu id đối tác",
            'address.required'        => "Thiếu địa chỉ",
            'longitude.unique'         => "Thiếu kinh độ",
            'latitude.required'       => "Thiếu vĩ độ",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first() . "."
            ], 200)
        );
    }
}
