<?php

namespace App\Http\Requests;

use App\Enums\ItemCategory;
use App\Enums\ItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchItemsRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::enum(ItemType::class)],
            'category' => ['nullable', Rule::enum(ItemCategory::class)],
            'city' => ['nullable', 'string', 'max:80'],
        ];
    }
}
