<?php

namespace App\Http\Requests\Admin\Vote;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoteRequest extends FormRequest
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
            'title' => 'required|string',
            'start_at' => 'required',
            'end_at' => 'required',
            // 'image' => 'nullable',
            /* 'start_at' => 'required|date|after_or_equal:now',
            'end_at'=> 'required|date|after:start_at' */
        ];
    }
}
