<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required' , 'string',"max:1500" , "min:3"],
            'url' => ['required' , 'string', 'url'],
            'content' => ['required' , 'string', "max:3000"]
        ];
    }

    public function attributes():array
    {
        return [
            'title' => 'judul',
            'url' => 'url',
            'content' => 'konten',
        ];
    }

    public function messages():array
    {
        return [
            'title.required' => 'Judul wajib diisi',
            'title.string' => 'Judul harus bertipe text',
            'title.max' => 'Judul maksimal 1500 karakter',
            'title.min' => "Judul minimal 3 karakter",
            'url.required' => 'URL wajib diisi',
            'url.string' => 'URL harus bertipe text',
            'url.url' => 'Format URL tidak valid. Gunakan format: https://contoh.com',
            'content.required' => 'Kontent wajib diisi',
            'content.string' => 'Kontent harus bertipe text',
            'content.max' => 'Kontent maksimal 3000 karakter',
        ];
    }
}
