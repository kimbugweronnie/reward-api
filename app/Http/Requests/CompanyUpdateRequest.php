<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
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
            "company_name" => 'nullable|string',
            "company_description"=> 'nullable|string',
            "company_email"=> 'nullable|email',
            "phone_prefix"=> 'nullable|string',
            "phone_number"=> 'nullable|string',
            "company_location"=> 'nullable|string', 
        ];
    }
}
