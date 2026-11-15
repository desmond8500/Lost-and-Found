<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Item;
use Database\Factories\ItemFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManageItemTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = ItemFactory::DEFAULT_TOKEN;

    public function test_author_sees_received_messages_with_a_valid_token(): void
    {
        $item = Item::factory()->create();
        ContactMessage::factory()->for($item)->create(['body' => 'Je crois que c\'est à moi !']);

        $this->get(route('items.manage', ['item' => $item, 'token' => self::TOKEN]))
            ->assertOk()
            ->assertSee('Je crois que c&#039;est à moi !', false);
    }

    public function test_invalid_token_returns_not_found(): void
    {
        $item = Item::factory()->create();

        $this->get(route('items.manage', ['item' => $item, 'token' => 'mauvais-jeton']))->assertNotFound();
        $this->patch(route('items.resolve', ['item' => $item, 'token' => 'mauvais-jeton']))->assertNotFound();
        $this->delete(route('items.destroy', ['item' => $item, 'token' => 'mauvais-jeton']))->assertNotFound();

        $this->assertModelExists($item);
        $this->assertNull($item->fresh()->resolved_at);
    }

    public function test_author_can_mark_item_as_returned(): void
    {
        $item = Item::factory()->create();

        $this->patch(route('items.resolve', ['item' => $item, 'token' => self::TOKEN]))
            ->assertRedirect(route('items.manage', ['item' => $item, 'token' => self::TOKEN]));

        $this->assertTrue($item->fresh()->isResolved());
    }

    public function test_author_can_delete_item_and_its_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('items/photo.jpg', 'image');
        $item = Item::factory()->create(['photo_path' => 'items/photo.jpg']);
        ContactMessage::factory()->for($item)->create();

        $this->delete(route('items.destroy', ['item' => $item, 'token' => self::TOKEN]))
            ->assertRedirect(route('items.index'));

        $this->assertModelMissing($item);
        $this->assertDatabaseCount('contact_messages', 0);
        Storage::disk('public')->assertMissing('items/photo.jpg');
    }
}
