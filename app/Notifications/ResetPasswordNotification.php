<?php

namespace App\Notifications;

use App\Services\EmailTemplateService;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = url(route("password.reset", [
            "token" => $this->token,
            "email" => $notifiable->getEmailForPasswordReset(),
        ], false));

        $data = [
            "user_name" => $notifiable->name ?? "",
            "user_email" => $notifiable->getEmailForPasswordReset(),
            "reset_url" => $resetUrl,
        ];

        $defaultSubject = "Redefinir PIN";
        $defaultHtml = view("emails.dynamic", [
            "html" => "<p>Olá {$data['user_name']},</p><p>Use o link abaixo para redefinir seu PIN:</p><p><a href=\"{$resetUrl}\">Redefinir PIN</a></p>",
        ])->render();

        $rendered = app(EmailTemplateService::class)->render(
            "password_reset",
            $data,
            $defaultSubject,
            $defaultHtml,
        );

        return (new MailMessage)
            ->subject($rendered["subject"])
            ->view("emails.dynamic", ["html" => $rendered["html"]]);
    }
}
