<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourtRequest extends FormRequest
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
            'name' => ['sometimes','string','max:255'],
            'location' => ['sometimes','nullable','string','max:255'],
            'description' => ['sometimes','nullable','string'],
            'hourly_rate' => ['sometimes','nullable','numeric','between:0,999999.99'],
        ];
    }
}
