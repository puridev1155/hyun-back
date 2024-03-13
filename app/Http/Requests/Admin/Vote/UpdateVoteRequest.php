<?php

namespace App\Http\Requests\Admin\Vote;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVoteRequest extends FormRequest
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
            'title' => 'sometimes|required|string',
            'start_at' => 'sometimes|nullable',
            'end_at'=> 'sometimes|nullable'
            //'start_at' => 'sometimes|required|date|after_or_equal:now',
            //'end_at'=> 'sometimes|required|date|after:start_at'
        ];
    }
}
