<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->employee !== null;
    }

    /** One requirement file: PDF, Word, or an image, up to 5 MB. */
    public function rules(): array
    {
        return [
            'submission_box_id' => ['required', 'integer', 'exists:submission_boxes,id'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Upload a PDF, Word document, or a JPG or PNG image.',
            'file.max' => 'The file must be 5 MB or smaller.',
        ];
    }
}
