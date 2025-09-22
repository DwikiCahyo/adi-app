<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


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
            'url'     => ['required','string','url','max:500'],
            'content' => ['required','string','max:3000'],
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

    protected function failedValidation(Validator $validator)
    {
        $id = $this->route('id'); // ambil id dari route

        throw new HttpResponseException(
            back()
                ->withErrors($validator, 'edit-'.$id) // 👉 error bag khusus edit-{id}
                ->withInput()
        );
    }
}
