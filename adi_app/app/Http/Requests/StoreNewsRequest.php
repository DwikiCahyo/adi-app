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
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'url' => 'nullable|url|max:500',
            'images' => 'nullable|array|max:10', // Maximum 10 images
            'images.*' => [
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:5120', // 5MB max per image
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000'
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul berita wajib diisi',
            'title.max' => 'Judul berita maksimal 255 karakter',
            'content.required' => 'Konten berita wajib diisi',
            'url.url' => 'Format URL tidak valid',
            'url.max' => 'URL maksimal 500 karakter',
            'images.max' => 'Maksimal 10 gambar yang dapat diupload',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Gambar harus berformat: jpeg, jpg, png, gif, atau webp',
            'images.*.max' => 'Ukuran gambar maksimal 5MB',
            'images.*.dimensions' => 'Dimensi gambar minimal 100x100 pixel dan maksimal 4000x4000 pixel',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'content' => 'konten',
            'url' => 'URL video',
            'images' => 'gambar',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up URL if provided
        if ($this->filled('url')) {
            $this->merge([
                'url' => trim($this->url)
            ]);
        }
    }
}