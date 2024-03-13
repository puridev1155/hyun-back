<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
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
            'name' => 'sometimes|max:255',//국가
            'email' => 'sometimes|email',//이메일
            'birth' => 'sometimes',//생년월일 8자리
            'gender' => 'sometimes',//성별
            'phone' => 'sometimes|nullable', //연락처
            'address' => 'sometimes|nullable', //주소
            'zipcode' => 'sometimes|nullable',
            'country_id' => 'sometimes',//국가 ID
            'height' => 'sometimes|nullable',//키
            'weight' => 'sometimes|nullable',//몸무게
            'instagram' => 'sometimes',//admin:1, manager:5, (default)member: 10
        ];
    }
}
