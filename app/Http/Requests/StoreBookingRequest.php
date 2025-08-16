<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'user_id' => ['required','exists:users,id'],
            'court_id' => ['required','exists:courts,id'],
            'start_time' => ['required','date'],
            'end_time' => ['required','date','after:start_time'],
            'status' => ['sometimes','in:pending,confirmed,cancelled'],
            'price' => ['nullable','numeric','between:0,999999.99'],
        ];
    }
}
