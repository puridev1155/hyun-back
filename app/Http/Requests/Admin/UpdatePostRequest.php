<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'category_id'=> 'sometimes|nullable',//카테고리ID
            'title' => 'sometimes|nullable',//제목
            'order' => 'sometimes|nullable',//순서
            'public' => 'sometimes|nullable',//공개 비공개 여부
            'content' => 'sometimes|nullable',//내용
            'client' => 'sometimes|nullable',
            'specs' => 'sometimes|nullable',
            'infos' => 'sometimes|nullable',
            'project_date' => 'sometimes|nullable', 
        ];
    }
}
