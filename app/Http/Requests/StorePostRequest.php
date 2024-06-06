<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\File;

class StorePostRequest extends FormRequest
{
    public static $extentions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'mp3', 'mp4', 'wav',
        'doc', 'docx', 'pdf', 'csv', 'xls', 'xlsx',
        'zip'
    ];
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
            'body' => ['nullable', 'string'],
            'attachments' => [
                'array',
                'max:50',
                function ($attribute, $value, $fail){
                    $totalSize = collect($value)->sum(function (UploadedFile $file) {
                        return $file->getSize();
                    });
                    if ($totalSize > 250000){    // 250 MB
                        $fail('The total size of all files must not exceed 250MB.');
                    }
                },
            ],
            'attachments.*' => [
                'file',
                File::types(self::$extentions),
            ],
            'user_id' => ['numeric']
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->user()->id,
            'body' => $this->input('body') ?: ''
        ]);
    }

    public function messages()
    {
        return [
            'attachments.*.file' => 'Each attachment must be a file.',
            'attachments.*.mimes' => 'Invalid file type for attachments.',
        ];
    }
}
