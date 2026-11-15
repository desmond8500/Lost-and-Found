<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sender_name' => ['required', 'string', 'max:80'],
            'sender_email' => ['required', 'email', 'max:160'],
            'body' => ['required', 'string', 'min:10', 'max:2000'],
            // Piège à robots : ce champ caché doit rester vide.
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'sender_name' => 'votre nom',
            'sender_email' => 'votre e-mail',
            'body' => 'message',
        ];
    }
}
