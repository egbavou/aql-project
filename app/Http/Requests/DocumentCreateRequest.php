<?php

namespace App\Http\Requests;

use App\Enum\Language;
use Illuminate\Foundation\Http\FormRequest;

class DocumentCreateRequest extends FormRequest
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
            'author'     => 'string|required',
            'title'      => 'string|required',
            'language'   => 'required|in:' . implode(',', Language::values()),
            'visibility' => 'required|in:public,private',
            'file'       => 'file|mimetypes:application/pdf|required|max:20480',
            'tags'       => 'array',
            'tags.*'     => 'string'
        ];
    }
}
