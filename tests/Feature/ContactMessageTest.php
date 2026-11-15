<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_contact_the_author_without_seeing_their_email(): void
    {
        Mail::fake();
        $item = Item::factory()->create(['contact_email' => 'auteur@example.com']);

        $this->post(route('items.messages.store', $item), [
            'sender_name' => 'Awa Ndiaye',
            'sender_email' => 'awa@example.com',
            'body' => 'Bonjour, je pense que c\'est ma carte, elle a un étui bleu.',
        ])->assertRedirect(route('items.show', $item));

        $this->assertDatabaseHas('contact_messages', [
            'item_id' => $item->id,
            'sender_email' => 'awa@example.com',
        ]);

        Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
            return $mail->hasTo('auteur@example.com')
                && $mail->hasReplyTo('awa@example.com');
        });
    }

    public function test_message_is_validated(): void
    {
        Mail::fake();
        $item = Item::factory()->create();

        $this->post(route('items.messages.store', $item), [
            'sender_email' => 'invalide',
            'body' => 'court',
        ])->assertSessionHasErrors(['sender_name', 'sender_email', 'body']);

        Mail::assertNothingSent();
    }

    public function test_resolved_items_cannot_be_contacted(): void
    {
        Mail::fake();
        $item = Item::factory()->resolved()->create();

        $this->post(route('items.messages.store', $item), [
            'sender_name' => 'Awa',
            'sender_email' => 'awa@example.com',
            'body' => 'Bonjour, est-ce toujours disponible ?',
        ])->assertStatus(410);

        Mail::assertNothingSent();
    }
}
