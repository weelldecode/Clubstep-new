<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ t('Subscription Deactivated') }}</title>
</head>

<body
    style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f9fafb; margin:0; padding:0;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <tr>
            <td style="padding: 24px; text-align: center; border-bottom: 1px solid #eaeaea;">
                <h1 style="margin: 0; font-weight: 700; font-size: 24px; color: #111827;">{{ t('Subscription Deactivated') }}</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px; color: #374151; font-size: 16px; line-height: 1.5;">
                <p>{{ t('Hello') }} <strong>{{ $user->name }}</strong> 👋,</p>
                <p>{{ t('Your subscription for the plan :plan was deactivated on :date.', ['plan' => $subscription->plan->name ?? t('unknown'), 'date' => $subscription->expires_at->format('d/m/Y')]) }}</p>
                <p>{{ t('If you want to keep enjoying our services, just renew your subscription by clicking the button below:') }}</p>
                <p style="text-align: center; margin: 32px 0;">
                    <a href="{{ url('/plans') }}"
                        style="background-color: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; font-weight: 600; border-radius: 6px; display: inline-block;">{{ t('Renew Subscription') }}</a>
                </p>
                <p style="color: #6b7280; font-size: 14px;">{{ t('If you have already renewed, please ignore this email.') }}</p>
                <p>{{ t('Thanks for being with us!') }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 16px; text-align: center; font-size: 12px; color: #9ca3af;">
                &copy; {{ date('Y') }} ClubStep. {{ t('All rights reserved.') }}
            </td>
        </tr>
    </table>
</body>

</html>
