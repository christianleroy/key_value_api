<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class KeyValueRequest extends FormRequest
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
            'key' => 'required|string',
            'value' => 'required',
//            'value' => 'required|array', // this is to be enabled if values must strictly be objects
        ];
    }

    public function messages(): array
    {
        // Custom message if values must strictly be an object
        return[
          'value.array' => 'The value must be an object.'
        ];
    }
}
