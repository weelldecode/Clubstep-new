<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class SubscriptionDeactivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Subscription $subscription;
    public string $subjectLine;
    public string $htmlBody;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Subscription $subscription)
    {
        $this->user = $user;
        $this->subscription = $subscription;

        $data = [
            "user_name" => $user->name ?? "",
            "user_email" => $user->email ?? "",
            "plan_name" => $subscription->plan?->name ?? "",
        ];

        $defaultSubject = "Sua assinatura foi desativada";
        $defaultHtml = view("emails.subscription_deactivated", [
            "user" => $this->user,
            "subscription" => $this->subscription,
        ])->render();

        $rendered = app(EmailTemplateService::class)->render(
            "subscription_deactivated",
            $data,
            $defaultSubject,
            $defaultHtml,
        );

        $this->subjectLine = $rendered["subject"];
        $this->htmlBody = $rendered["html"];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->htmlBody,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
