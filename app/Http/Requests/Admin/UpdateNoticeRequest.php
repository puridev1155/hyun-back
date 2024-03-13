<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoticeRequest extends FormRequest
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
            "public" => 'sometimes|required',
            "category_id" => 'sometimes|required',
            "title" => 'sometimes|required',
            "info" => 'sometimes|required',
            "user_type" => 'sometimes|required',
        ];
    }
}
