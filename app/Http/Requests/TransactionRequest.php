<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'purchase_amount' => 'required|integer',
            'payment_mode' => 'required|string',
            'points_awarded' => 'required|integer',
            'subscription_id' => 'required|integer',
            'user_id' => 'required|integer',
            'program_id' => 'required|integer',
            'merchant_id' => 'required|integer',
            'receipt_number' => 'nullable|string',
        ];
       
    }
}
