<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'franchise' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'tmdb_id' => 'nullable|integer',
            'tmdb_type' => 'required|in:movie,tv',
            'is_active' => 'sometimes|boolean',
            'images' => 'nullable|array|max:5',
            'images.*' => [
                'required',
                File::image()
                    ->types(['jpeg', 'jpg', 'png', 'webp'])
                    ->max('2mb')
            ]
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Ürün Adı',
            'category_id' => 'Kategori',
            'franchise' => 'Franchise',
            'description' => 'Açıklama',
            'price' => 'Fiyat',
            'stock' => 'Stok',
            'tmdb_id' => 'TMDB ID',
            'tmdb_type' => 'TMDB Türü',
            'is_active' => 'Satışta mı',
            'images' => 'Görseller',
            'images.*' => 'Görsel'
        ];
    }
}
