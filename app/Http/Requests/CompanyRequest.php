<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
            "company_name" => 'required|string',
            "company_description"=> 'nullable|string',
            "company_email"=> 'required|email',
            "phone_prefix"=> 'required|string',
            "phone_number"=> 'required|string',
            "company_location"=> 'nullable|string', 
        ];
    }
}
