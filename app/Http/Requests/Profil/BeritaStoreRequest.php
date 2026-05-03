<?php

namespace App\Http\Requests\Profil;

use Illuminate\Foundation\Http\FormRequest;

class BeritaStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type' => ['required','in:news,education'],
            'title' => ['required','string','max:180'],
            'slug' => ['nullable','string','max:220'],
            'excerpt' => ['nullable','string','max:320'],
            'category_label' => ['nullable','string','max:80'],
            'content' => ['nullable','string'],
            'published_at' => ['nullable','date'],
            'is_published' => ['nullable','boolean'],

            'cover' => ['nullable','image','max:4096'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
        ]);
    }
}
