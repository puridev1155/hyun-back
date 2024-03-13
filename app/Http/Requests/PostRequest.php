<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
                'id' => 'sometimes|nullable', //갯수
                'instagram'=> 'nullable',//인스타그램
                'participant' => 'nullable', //참여자명
                'participant_count' => 'nullable', //참여자수
                'video_url' => 'nullable', //영상
                'phone' => 'required', //연락처
                'status' => 'nullable', //결제 여부
            ]; } else {
                return [
                    'pay_id' => 'required',//POST ID
                    'price' => 'nullable', //가격
                    'price_name' => 'nullable', //표 이름
                    'amount' => 'nullable', //갯수
                    'status' => 'nullable', //결제 여부
                ];
            }
    }
}
