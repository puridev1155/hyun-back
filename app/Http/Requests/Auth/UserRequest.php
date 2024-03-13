<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $result = [
            'name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'password' => 'required|min:6',
        ];

        if ($this->method() === 'PUT') {
            $result = [
                'name' => 'sometimes|nullable',
            ];
        }

        return $result;
    }
}
