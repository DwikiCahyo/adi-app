<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'agenda' => ['nullable', 'string', 'max:255'],
            'title' => ['required','string','max:255'],
            'topics.*.topic' => ['required','string','max:255'],
            'topics.*.content' => ['required','string'],
            'images.*' => ['nullable','mimes:jpg,jpeg,png,gif' ,'max:2048']
        ];
    }

    public function messages():array
    {
        return [
            'agenda.string' => 'Agenda harus bertipe text',
            'agenda.max' => 'Agenda maksimal 255 karakter',
            'title.required' => 'Agenda wajib diisi',
            'title.string' => 'Judul harus bertipe text',
            'title.max' => 'Judul maksimal 255 karakter',
            'topics.*.topic.required' => 'Judul topik yang ditambahkan wajib diisi',
            'topics.*.topic.string' => 'Judul topik yang ditambahkan harus bertipe text',
            'topics.*.topic.max' => 'Judul topik yang ditambahkan maksimal 255 karakter',
            'topics.*.content.required' => 'Konten topik yang ditambahkan wajib diisi',
            'topics.*.content.string' => 'Konten topik yang ditambahkan harus bertipe text',
            'images.*.mimes' => 'File yang ditambakan harus bertipe jpg,jpeg,png,gif',
            'images.*.max' => 'Fiel yang ditambahkan maksimal 2 MB',
        ];
    }
}
