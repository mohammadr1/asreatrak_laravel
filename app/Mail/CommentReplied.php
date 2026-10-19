<?php

namespace App\Mail;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommentReplied extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Comment $comment, public Comment $reply)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'پاسخ به نظر شما در ' . config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.comment-replied');
    }
}
