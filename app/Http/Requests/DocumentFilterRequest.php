<?php

namespace App\Http\Requests;

use App\Enum\Language;
use Illuminate\Foundation\Http\FormRequest;

class DocumentFilterRequest extends FormRequest
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
            'author'   => 'string|nullable',
            'tag'      => 'numeric|nullable',
            'language' => 'nullable|in:' . implode(',', Language::values()),
            'title'    => 'string|nullable',
            'page'     => 'numeric|integer|min:1|nullable',
            'per_page' => 'numeric|integer|min:1|nullable'
        ];
    }
}
