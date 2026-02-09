<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ t('Subscription Expiring Soon') }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h2>{{ t('Hello') }}, {{ $user->name }}! 👋</h2>
    <p>{{ t('Your subscription is about to expire in :days days.', ['days' => $daysLeft]) }}</p>
    
    <p>{{ t('To keep enjoying our services, please renew your subscription before it expires.') }}</p>
    <a href="{{ $renewUrl }}" style="padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">
    {{ t('Renew Subscription') }}
</a>


    <p>{{ t('If you need help, we are here for you!') }}</p>

    <p>{{ t('Thanks for being with us!') }} 🎉</p>
    <hr>
    <p style="font-size: 0.9em; color: #777;">{{ t('This is an automated notice, please do not reply to this email.') }}</p>
</body>
</html>
