<?php

namespace App\Http\Requests;

use App\Enums\Entry\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEntryRequest extends FormRequest
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
            'status' => [
                'sometimes',
                Rule::in(array_column(Status::cases(), 'value')),
            ],
            'content' => ['sometimes', 'string', 'max:1000'],
        ];
    }
}
