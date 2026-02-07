<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
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

        if($this->isMethod('post')){
            return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'governorate' => 'required|string|max:100',
            'rooms' => 'required|integer|min:1',
            'area' => 'required|numeric|min:1',

            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            ];
        }

        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'location' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:500',
            'images' => 'sometimes|array|min:1',
            'city' => 'sometimes|string|max:100',
            'governorate' => 'sometimes|string|max:100',
            'rooms' => 'sometimes|integer|min:1',
            'area' => 'sometimes|numeric|min:1',
            'images.*' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'sometimes|in:available,rented,maintenance',
        ];
        
    }
}
