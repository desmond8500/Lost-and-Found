<?php

namespace App\Models;

use App\Enums\ItemCategory;
use App\Enums\ItemType;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'type',
    'category',
    'title',
    'description',
    'name_on_item',
    'city',
    'location',
    'occurred_on',
    'photo_path',
    'contact_name',
    'contact_email',
])]
#[Hidden(['contact_email', 'manage_token_hash'])]
class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ItemType::class,
            'category' => ItemCategory::class,
            'occurred_on' => 'date',
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * Messages envoyés à l'auteur de l'annonce.
     *
     * @return HasMany<ContactMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    /**
     * Annonces encore ouvertes (objet pas encore rendu).
     */
    #[Scope]
    protected function open(Builder $query): void
    {
        $query->whereNull('resolved_at');
    }

    /**
     * Recherche plein texte simple sur les champs utiles.
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $query) use ($term) {
            $query->whereLike('title', "%{$term}%")
                ->orWhereLike('name_on_item', "%{$term}%")
                ->orWhereLike('description', "%{$term}%")
                ->orWhereLike('location', "%{$term}%");
        });
    }

    /**
     * Filtres de recherche (site web et API) : q, type, category, city.
     *
     * @param  array<string, mixed>  $filters
     */
    #[Scope]
    protected function filter(Builder $query, array $filters): void
    {
        $query->search($filters['q'] ?? null)
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['category'] ?? null, fn (Builder $query, string $category) => $query->where('category', $category))
            ->when($filters['city'] ?? null, fn (Builder $query, string $city) => $query->whereLike('city', "%{$city}%"));
    }

    /**
     * Génère un nouveau jeton de gestion, stocke son empreinte
     * et retourne le jeton en clair (à ne transmettre qu'à l'auteur).
     */
    public function issueManageToken(): string
    {
        $token = Str::random(48);

        $this->manage_token_hash = hash('sha256', $token);

        return $token;
    }

    /**
     * Vérifie le jeton de gestion fourni dans l'URL.
     */
    public function hasManageToken(string $token): bool
    {
        return hash_equals($this->manage_token_hash, hash('sha256', $token));
    }

    /**
     * Lien secret permettant à l'auteur de gérer son annonce.
     */
    public function manageUrl(string $token): string
    {
        return route('items.manage', ['item' => $this, 'token' => $token]);
    }

    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }

    /**
     * Supprime l'annonce, sa photo et (en cascade) ses messages.
     */
    public function deleteWithPhoto(): void
    {
        if ($this->photo_path) {
            Storage::disk('public')->delete($this->photo_path);
        }

        $this->delete();
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path
            ? Storage::disk('public')->url($this->photo_path)
            : null;
    }
}
