<?php

namespace Tests\Feature;

use App\Enums\ItemCategory;
use App\Enums\ItemType;
use App\Mail\ItemPublished;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_lists_open_items_only(): void
    {
        Item::factory()->create(['title' => 'Clés de voiture']);
        Item::factory()->resolved()->create(['title' => 'Téléphone déjà rendu']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Clés de voiture')
            ->assertDontSee('Téléphone déjà rendu');
    }

    public function test_owner_can_find_an_identity_card_by_name(): void
    {
        Item::factory()->found()->identityCard('Awa Ndiaye')->create();
        Item::factory()->found()->identityCard('Moussa Diop')->create();

        $this->get('/?q=Ndiaye')
            ->assertOk()
            ->assertSee('Awa Ndiaye')
            ->assertDontSee('Moussa Diop');
    }

    public function test_items_can_be_filtered_by_type_category_and_city(): void
    {
        Item::factory()->lost()->create(['category' => ItemCategory::Keys, 'city' => 'Dakar', 'title' => 'Trousseau rouge']);
        Item::factory()->found()->create(['category' => ItemCategory::Keys, 'city' => 'Dakar', 'title' => 'Trousseau bleu']);
        Item::factory()->lost()->create(['category' => ItemCategory::Phone, 'city' => 'Dakar', 'title' => 'iPhone noir']);
        Item::factory()->lost()->create(['category' => ItemCategory::Keys, 'city' => 'Thiès', 'title' => 'Trousseau vert']);

        $this->get('/?type=lost&category=keys&city=dakar')
            ->assertOk()
            ->assertSee('Trousseau rouge')
            ->assertDontSee('Trousseau bleu')
            ->assertDontSee('iPhone noir')
            ->assertDontSee('Trousseau vert');
    }

    public function test_create_form_is_displayed(): void
    {
        $this->get('/annonces/nouvelle?type=lost')
            ->assertOk()
            ->assertSee('Publier une annonce');
    }

    public function test_item_can_be_published_and_author_receives_manage_link(): void
    {
        Mail::fake();
        Storage::fake('public');

        $response = $this->post('/annonces', [
            'type' => ItemType::Found->value,
            'category' => ItemCategory::IdentityCard->value,
            'title' => "Carte d'identité trouvée au marché",
            'name_on_item' => 'Awa Ndiaye',
            'city' => 'Dakar',
            'location' => 'Marché Sandaga',
            'occurred_on' => now()->toDateString(),
            'photo' => UploadedFile::fake()->image('carte.jpg'),
            'contact_name' => 'Ibrahima',
            'contact_email' => 'ibrahima@example.com',
        ]);

        $item = Item::sole();

        $this->assertSame(ItemType::Found, $item->type);
        $this->assertSame('Awa Ndiaye', $item->name_on_item);
        Storage::disk('public')->assertExists($item->photo_path);

        $sentToken = null;
        Mail::assertSent(ItemPublished::class, function (ItemPublished $mail) use ($item, &$sentToken) {
            $sentToken = $mail->token;

            return $mail->hasTo('ibrahima@example.com') && $mail->item->is($item);
        });

        $this->assertTrue($item->hasManageToken($sentToken));
        $response->assertRedirect($item->manageUrl($sentToken));
    }

    public function test_publication_is_validated(): void
    {
        Mail::fake();

        $this->post('/annonces', [
            'type' => 'stolen',
            'occurred_on' => now()->addDay()->toDateString(),
            'contact_email' => 'pas-un-email',
        ])->assertSessionHasErrors(['type', 'category', 'title', 'city', 'occurred_on', 'contact_name', 'contact_email']);

        $this->assertDatabaseCount('items', 0);
        Mail::assertNothingSent();
    }

    public function test_bots_filling_the_honeypot_are_rejected(): void
    {
        Mail::fake();

        $this->post('/annonces', [
            'type' => ItemType::Lost->value,
            'category' => ItemCategory::Keys->value,
            'title' => 'Clés',
            'city' => 'Dakar',
            'occurred_on' => now()->toDateString(),
            'contact_name' => 'Bot',
            'contact_email' => 'bot@example.com',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('items', 0);
    }

    public function test_item_page_never_reveals_the_author_email(): void
    {
        $item = Item::factory()->create(['contact_email' => 'secret@example.com']);

        $this->get(route('items.show', $item))
            ->assertOk()
            ->assertSee($item->title)
            ->assertDontSee('secret@example.com');
    }
}
