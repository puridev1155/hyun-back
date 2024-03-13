<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetPostRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'sometimes|nullable', //갯수
            'instagram'=> 'nullable',//인스타그램
            'participant' => 'nullable', //참여자명
            'phone' => 'required', //연락처
            'status' => 'nullable', //결제 여부
        ];
    }
}
