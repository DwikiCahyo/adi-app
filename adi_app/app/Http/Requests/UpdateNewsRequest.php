<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'   => ['required','string','max:1500','min:3'],
            'url'     => ['nullable','string','url','max:500'],
            'content' => ['nullable','string','max:3000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title'   => 'judul',
            'url'     => 'URL',
            'content' => 'konten',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'title.string'   => 'Judul harus berupa teks.',
            'title.max'      => 'Judul maksimal 1500 karakter.',
            'title.min'      => 'Judul minimal 3 karakter.',

            'url.url'        => 'Format URL tidak valid. Gunakan format: https://contoh.com',
            'url.max'        => 'URL maksimal 500 karakter.',

            'content.string' => 'Konten harus berupa teks.',
            'content.max'    => 'Konten maksimal 3000 karakter.',
        ];
    }
}
