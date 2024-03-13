<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('PUT')) {
        return [
            'id' => 'required',
            'amount' => 'sometimes|nullable', //갯수
            'status' => 'sometimes|nullable',
            'address' => 'required', //주소
            'zipcode' => 'required', //우편번호
        ]; } else {
            return [
                'pay_id' => 'required',//POST ID
                'price' => 'required', //가격
                'price_name' => 'nullable', //가격
                'amount' => 'required', //갯수
                'status' => 'nullable', //결제 여부
            ];
        }
    }
}
