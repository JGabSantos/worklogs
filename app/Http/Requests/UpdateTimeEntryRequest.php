<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTimeEntryRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i:s'],
            'end_time' => ['required', 'date_format:H:i:s'],
            'location' => ['required', 'string', 'max:255'],
            'activity_type_id' => ['required', 'integer', 'exists:activity_types,id'],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:draft,active'],
        ];
    }
}
