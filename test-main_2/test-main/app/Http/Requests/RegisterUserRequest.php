<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'first_name'=> 'required|string|max:255',
            'last_name'=> 'required|string|max:255',
            'phone_number' => 'required|digits:10|unique:users,phone_number',
            'user_state' => 'required|in:owner,tenant',
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'nation_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'date_of_birth' => 'required|date|before:today'
            
        ];
    }
}
