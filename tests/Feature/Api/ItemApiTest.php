<?php

namespace Tests\Feature\Api;

use App\Mail\ContactMessageReceived;
use App\Mail\ItemPublished;
use App\Models\ContactMessage;
use App\Models\Item;
use Database\Factories\ItemFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemApiTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = ItemFactory::DEFAULT_TOKEN;

    public function test_meta_lists_types_and_categories(): void
    {
        $this->getJson('/api/v1/meta')
            ->assertOk()
            ->assertJsonPath('types.0', ['value' => 'lost', 'label' => 'Perdu'])
            ->assertJsonFragment(['value' => 'identity_card', 'label' => "Carte d'identité"]);
    }

    public function test_items_are_listed_filtered_and_never_expose_the_author_email(): void
    {
        Item::factory()->found()->identityCard('Awa Ndiaye')->create(['contact_email' => 'secret@example.com']);
        Item::factory()->found()->identityCard('Moussa Diop')->create();
        Item::factory()->resolved()->identityCard('Awa Ndiaye')->create();

        $response = $this->getJson('/api/v1/items?q=Ndiaye&type=found')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name_on_item', 'Awa Ndiaye')
            ->assertJsonPath('data.0.type.label', 'Trouvé')
            ->assertJsonPath('meta.total', 1)
            ->assertJsonMissingPath('data.0.contact_email')
            ->assertJsonMissingPath('data.0.manage_token_hash')
            ->assertJsonMissingPath('data.0.messages');

        $this->assertStringNotContainsString('secret@example.com', $response->getContent());
    }

    public function test_invalid_filters_return_json_errors(): void
    {
        $this->getJson('/api/v1/items?type=stolen')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('type');
    }

    public function test_item_can_be_shown(): void
    {
        $item = Item::factory()->create();

        $this->getJson("/api/v1/items/{$item->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $item->id)
            ->assertJsonPath('data.web_url', route('items.show', $item));
    }

    public function test_item_can_be_published_with_a_photo_and_returns_its_manage_token(): void
    {
        Mail::fake();
        Storage::fake('public');

        $response = $this->postJson('/api/v1/items', [
            'type' => 'lost',
            'category' => 'passport',
            'title' => 'Passeport perdu',
            'name_on_item' => 'Fatou Sow',
            'city' => 'Dakar',
            'occurred_on' => now()->toDateString(),
            'photo' => UploadedFile::fake()->image('passeport.jpg'),
            'contact_name' => 'Fatou',
            'contact_email' => 'fatou@example.com',
        ])->assertCreated()
            ->assertJsonPath('data.title', 'Passeport perdu')
            ->assertJsonPath('data.category.is_official_document', true);

        $item = Item::sole();
        $token = $response->json('manage_token');

        $this->assertTrue($item->hasManageToken($token));
        $response->assertJsonPath('manage_url', $item->manageUrl($token));
        Storage::disk('public')->assertExists($item->photo_path);
        Mail::assertSent(ItemPublished::class, fn (ItemPublished $mail) => $mail->hasTo('fatou@example.com'));
    }

    public function test_publication_errors_are_returned_as_json(): void
    {
        $this->postJson('/api/v1/items', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['type', 'category', 'title', 'city', 'occurred_on', 'contact_name', 'contact_email']);
    }

    public function test_message_can_be_sent_to_the_author(): void
    {
        Mail::fake();
        $item = Item::factory()->create(['contact_email' => 'auteur@example.com']);

        $this->postJson("/api/v1/items/{$item->id}/messages", [
            'sender_name' => 'Awa',
            'sender_email' => 'awa@example.com',
            'body' => 'Bonjour, je crois que c\'est ma carte.',
        ])->assertCreated();

        $this->assertDatabaseHas('contact_messages', ['item_id' => $item->id, 'sender_email' => 'awa@example.com']);
        Mail::assertSent(ContactMessageReceived::class, fn ($mail) => $mail->hasTo('auteur@example.com'));
    }

    public function test_resolved_item_cannot_receive_messages(): void
    {
        $item = Item::factory()->resolved()->create();

        $this->postJson("/api/v1/items/{$item->id}/messages", [
            'sender_name' => 'Awa',
            'sender_email' => 'awa@example.com',
            'body' => 'Est-ce toujours disponible ?',
        ])->assertStatus(410);
    }

    public function test_author_can_read_messages_with_the_token_header(): void
    {
        $item = Item::factory()->create();
        ContactMessage::factory()->for($item)->create(['sender_email' => 'awa@example.com']);

        $this->getJson("/api/v1/items/{$item->id}/manage", ['X-Manage-Token' => self::TOKEN])
            ->assertOk()
            ->assertJsonCount(1, 'data.messages')
            ->assertJsonPath('data.messages.0.sender_email', 'awa@example.com');
    }

    public function test_management_requires_a_valid_token(): void
    {
        $item = Item::factory()->create();

        $this->getJson("/api/v1/items/{$item->id}/manage")->assertNotFound();
        $this->patchJson("/api/v1/items/{$item->id}/resolve", [], ['X-Manage-Token' => 'faux'])->assertNotFound();
        $this->deleteJson("/api/v1/items/{$item->id}", [], ['X-Manage-Token' => 'faux'])->assertNotFound();

        $this->assertModelExists($item);
        $this->assertFalse($item->fresh()->isResolved());
    }

    public function test_author_can_mark_as_returned_and_delete(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('items/photo.jpg', 'image');
        $item = Item::factory()->create(['photo_path' => 'items/photo.jpg']);

        $this->patchJson("/api/v1/items/{$item->id}/resolve", [], ['X-Manage-Token' => self::TOKEN])
            ->assertOk()
            ->assertJsonPath('data.is_resolved', true);

        $this->deleteJson("/api/v1/items/{$item->id}", [], ['X-Manage-Token' => self::TOKEN])
            ->assertNoContent();

        $this->assertModelMissing($item);
        Storage::disk('public')->assertMissing('items/photo.jpg');
    }
}
