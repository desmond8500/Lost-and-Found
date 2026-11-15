<?php

namespace App\Http\Requests;

use App\Enums\ItemCategory;
use App\Enums\ItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(ItemType::class)],
            'category' => ['required', Rule::enum(ItemCategory::class)],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'name_on_item' => ['nullable', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:80'],
            'location' => ['nullable', 'string', 'max:160'],
            'occurred_on' => ['required', 'date', 'before_or_equal:today'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'contact_name' => ['required', 'string', 'max:80'],
            'contact_email' => ['required', 'email', 'max:160'],
            // Piège à robots : ce champ caché doit rester vide.
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'occurred_on.before_or_equal' => 'La date ne peut pas être dans le futur.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'type' => 'type d\'annonce',
            'category' => 'catégorie',
            'title' => 'titre',
            'description' => 'description',
            'name_on_item' => 'nom inscrit sur l\'objet',
            'city' => 'ville',
            'location' => 'lieu',
            'occurred_on' => 'date',
            'photo' => 'photo',
            'contact_name' => 'votre nom',
            'contact_email' => 'votre e-mail',
        ];
    }
}
