<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantRequest extends FormRequest
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
            "merchant_name" => 'required|string',
            "merchant_description"=> 'required|string',
            "email"=> 'required|email',
            "password" => 'required|string',
            "phone_prefix"=> 'required|string',
            "phone_number"=> 'required|string',
            "location"=> 'required|string',
            "user_type"=> 'required|string'     
        ];
    }
}
