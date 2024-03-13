<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
        return [
            'category_id'=> 'nullable',
            'brand'=> 'nullable',
            'product_name'=> 'nullable',
            'in_stock'=> 'nullable',
            'out_stock'=> 'nullable',
            'description'=> 'nullable',
            'info'=> 'nullable',
            'lang_id'=>'nullable',
            'shipping'=> 'nullable'
        ];
    }
}
