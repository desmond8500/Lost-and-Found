<?php

namespace App\Enums;

enum ItemCategory: string
{
    case IdentityCard = 'identity_card';
    case Passport = 'passport';
    case DriverLicense = 'driver_license';
    case StudentCard = 'student_card';
    case Wallet = 'wallet';
    case Keys = 'keys';
    case Phone = 'phone';
    case Other = 'other';

    /**
     * Libellé affiché dans l'interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::IdentityCard => "Carte d'identité",
            self::Passport => 'Passeport',
            self::DriverLicense => 'Permis de conduire',
            self::StudentCard => 'Carte étudiant',
            self::Wallet => 'Portefeuille / sac',
            self::Keys => 'Clés',
            self::Phone => 'Téléphone',
            self::Other => 'Autre',
        };
    }

    /**
     * Indique si la catégorie est un document officiel
     * (on affiche alors des conseils de confidentialité).
     */
    public function isOfficialDocument(): bool
    {
        return in_array($this, [
            self::IdentityCard,
            self::Passport,
            self::DriverLicense,
            self::StudentCard,
        ], true);
    }
}
