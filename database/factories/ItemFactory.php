<?php

namespace Database\Factories;

use App\Enums\ItemCategory;
use App\Enums\ItemType;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Jeton de gestion utilisé par défaut (pratique pour les tests).
     */
    public const DEFAULT_TOKEN = 'jeton-de-test';

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = fake()->randomElement(ItemCategory::cases());

        return [
            'type' => fake()->randomElement(ItemType::cases()),
            'category' => $category,
            'title' => $category->label().' '.fake()->randomElement(['bleu', 'noir', 'marron', 'avec étui', 'récent']),
            'description' => fake()->optional()->sentence(12),
            'name_on_item' => $category->isOfficialDocument() ? fake()->name() : null,
            'city' => fake()->randomElement(['Dakar', 'Thiès', 'Saint-Louis', 'Ziguinchor', 'Kaolack', 'Rufisque']),
            'location' => fake()->optional()->streetName(),
            'occurred_on' => fake()->dateTimeBetween('-2 months'),
            'contact_name' => fake()->firstName(),
            'contact_email' => fake()->safeEmail(),
            'manage_token_hash' => hash('sha256', self::DEFAULT_TOKEN),
        ];
    }

    public function lost(): static
    {
        return $this->state(fn () => ['type' => ItemType::Lost]);
    }

    public function found(): static
    {
        return $this->state(fn () => ['type' => ItemType::Found]);
    }

    public function identityCard(?string $name = null): static
    {
        return $this->state(fn () => [
            'category' => ItemCategory::IdentityCard,
            'title' => "Carte d'identité",
            'name_on_item' => $name ?? fake()->name(),
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn () => ['resolved_at' => now()]);
    }
}
