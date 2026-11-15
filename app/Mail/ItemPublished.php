<?php

namespace App\Mail;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ItemPublished extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Item $item,
        public string $token,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre annonce « '.$this->item->title.' » est en ligne',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.item-published',
            with: [
                'manageUrl' => $this->item->manageUrl($this->token),
                'showUrl' => route('items.show', $this->item),
            ],
        );
    }
}
