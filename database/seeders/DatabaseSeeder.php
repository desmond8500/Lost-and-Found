<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Toutes les annonces de démo partagent le jeton de gestion
     * « jeton-de-test » : /annonces/{id}/gerer/jeton-de-test
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Item::factory()->found()->identityCard('Awa Ndiaye')->create([
            'city' => 'Dakar',
            'location' => 'Gare routière des Baux Maraîchers',
        ]);

        Item::factory()->lost()->identityCard('Moussa Diop')->create([
            'city' => 'Thiès',
        ]);

        Item::factory(14)
            ->has(ContactMessage::factory()->count(2), 'messages')
            ->create();

        Item::factory(3)->resolved()->create();
    }
}
