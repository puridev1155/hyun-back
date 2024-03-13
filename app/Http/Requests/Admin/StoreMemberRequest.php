<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
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
            'name' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'required|min:3', // password
            'birth' => 'required', //생년월일
            'country_id' => 'required',//국가
            'role' => 'required',//admin:1, manager:5, (default)member: 10
            'membership' => 'nullable',
            // 'is_agree' => 'required',//약관 동의
            // 'block_notice' => 'required'//공지 동의
        ];
    }
}
