<?php

/*
| Traductions françaises des règles de validation utilisées par l'application.
| Les règles absentes retombent sur l'anglais (APP_FALLBACK_LOCALE).
*/

return [
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale au :date.',
    'date' => 'Le champ :attribute n\'est pas une date valide.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'enum' => 'La valeur sélectionnée pour :attribute est invalide.',
    'image' => 'Le champ :attribute doit être une image.',
    'max' => [
        'array' => 'Le champ :attribute ne doit pas contenir plus de :max éléments.',
        'file' => 'Le fichier :attribute ne doit pas dépasser :max Ko.',
        'numeric' => 'Le champ :attribute ne doit pas dépasser :max.',
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],
    'mimes' => 'Le fichier :attribute doit être de type : :values.',
    'min' => [
        'array' => 'Le champ :attribute doit contenir au moins :min éléments.',
        'file' => 'Le fichier :attribute doit faire au moins :min Ko.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'prohibited' => 'Le champ :attribute doit rester vide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être un texte.',
    'uploaded' => 'Le fichier :attribute n\'a pas pu être envoyé.',

    'attributes' => [],
];
