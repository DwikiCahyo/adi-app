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
            'url'     => ['nullable','string','url','max:500'], // FIXED: Made URL nullable for updates
            'content' => ['required','string','max:3000'],
            'images' => 'nullable|array|max:10', // Handle new images
            'images.*' => [
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:5120', // 5MB max per image
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000'
            ],
            'remove_images' => 'nullable|string', // Handle comma-separated image IDs to remove
        ];
    }

    public function attributes(): array
    {
        return [
            'title'   => 'judul',
            'url'     => 'URL',
            'content' => 'konten',
            'images' => 'gambar',
            'remove_images' => 'gambar yang akan dihapus',
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

            'content.required' => 'Konten wajib diisi.',
            'content.string' => 'Konten harus berupa teks.',
            'content.max'    => 'Konten maksimal 3000 karakter.',
            
            // Image validation messages
            'images.max' => 'Maksimal 10 gambar yang dapat diupload.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.mimes' => 'Gambar harus berformat: jpeg, jpg, png, gif, atau webp.',
            'images.*.max' => 'Ukuran gambar maksimal 5MB.',
            'images.*.dimensions' => 'Dimensi gambar minimal 100x100 pixel dan maksimal 4000x4000 pixel.',
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
        
        // Clean up remove_images field
        if ($this->filled('remove_images')) {
            $this->merge([
                'remove_images' => trim($this->remove_images)
            ]);
        }
    }

    protected function failedValidation(Validator $validator)
    {
        // Get the news ID from the route
        $newsId = $this->route('news') ? $this->route('news')->id : $this->route('id');

        throw new HttpResponseException(
            back()
                ->withErrors($validator, 'edit-'.$newsId) // Error bag specific to edit-{id}
                ->withInput()
        );
    }
}