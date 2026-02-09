<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionExpiringMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionExpiringEmails extends Command
{



    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:notify-expiring';

    /**
     * The console command description.
     *
     * @var string
     */


    protected $description = 'Enviar email para usuários com assinaturas próximas do vencimento';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereHas('subscriptions', function ($query) {
            $query->where('status', 'active')
                ->where('notified_expiring', false)
                ->whereBetween('expires_at', [now(), now()->addDays(7)]);
        })->get();

        foreach ($users as $user) {
            // Pega a assinatura ativa que está expirando e não foi notificada
            $subscription = $user->subscriptions()
                ->where('status', 'active')
                ->where('notified_expiring', false)
                ->whereBetween('expires_at', [now(), now()->addDays(7)])
                ->first();

            if (!$subscription) {
                continue;
            }
            $expiresAt = \Carbon\Carbon::parse($subscription->expires_at);
            $daysLeft = now()->diffInDays($expiresAt); 
            Mail::to($user->email)->send(new SubscriptionExpiringMail($user, round($daysLeft), $subscription));

            $subscription->update(['notified_expiring' => true]);

            $this->info("Email enviado para {$user->email} (faltam {$daysLeft} dias)");
        }
    }
}
