<?php

namespace App\Enums;

enum ItemType: string
{
    case Lost = 'lost';
    case Found = 'found';

    /**
     * Libellé affiché dans l'interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Lost => 'Perdu',
            self::Found => 'Trouvé',
        };
    }

    /**
     * Classes Tailwind du badge associé.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Lost => 'bg-red-100 text-red-800',
            self::Found => 'bg-emerald-100 text-emerald-800',
        };
    }
}
