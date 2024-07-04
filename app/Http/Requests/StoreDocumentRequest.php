<?php

namespace App\Http\Requests;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Document::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'document' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf'],
        ];
    }

    public function messages()
    {
        return [
            'document.required' => 'A PDF file is required.',
            'document.mimes' => 'Only PDF files are allowed.',
        ];
    }
}
