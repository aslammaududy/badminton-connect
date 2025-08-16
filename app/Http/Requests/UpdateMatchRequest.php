<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMatchRequest extends FormRequest
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
            'organizer_id' => ['sometimes','exists:users,id'],
            'tournament_id' => ['sometimes','nullable','exists:tournaments,id'],
            'court_id' => ['sometimes','nullable','exists:courts,id'],
            'start_time' => ['sometimes','date'],
            'end_time' => ['sometimes','nullable','date','after:start_time'],
            'status' => ['sometimes','in:scheduled,completed,cancelled'],
        ];
    }
}
