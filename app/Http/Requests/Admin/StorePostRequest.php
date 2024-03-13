<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'category_id'=> 'required',//카테고리ID
            'title' => 'required',//제목
            'order' => 'nullable',//순서
            'public' => 'required',//공개 비공개 여부
            'content' => 'nullable',//내용
            'client' => 'nullable',
            'specs' => 'nullable',
            'infos' => 'nullable',
            'project_date' => 'nullable', 
        ];
    }
}
