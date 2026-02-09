<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class SubscriptionExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

public $user;
public $daysLeft;
public $renewUrl;
public string $subjectLine;
public string $htmlBody;


    /**
     * Create a new message instance.
     */
 public function __construct($user, $daysLeft, $subscription)
{
    $this->user = $user;
    $this->daysLeft = $daysLeft;
    $this->renewUrl = route('checkout.renew', ['id' => $subscription->plan_id, 'sub_id' => $subscription->id]);

    $data = [
        "user_name" => $user->name ?? "",
        "user_email" => $user->email ?? "",
        "days_left" => $daysLeft,
        "renew_url" => $this->renewUrl,
    ];

    $defaultSubject = "Sua assinatura está expirando";
    $defaultHtml = view("emails.subscription_expiring", [
        "user" => $this->user,
        "daysLeft" => $this->daysLeft,
        "renewUrl" => $this->renewUrl,
    ])->render();

    $rendered = app(EmailTemplateService::class)->render(
        "subscription_expiring",
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
