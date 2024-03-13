<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
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
            "country_id" => 'sometimes|required',
            "category_id" => 'sometimes|required',
            "banner_title" => 'sometimes|required',

            "order" => 'sometimes|nullable',
            "location" => 'sometimes|nullable',
        ];
    }
}
