<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionDeactivatedMail;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DeactivateExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desativa assinaturas que expiraram';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $users = User::whereHas('subscriptions', function ($q) use ($now) {
            $q->where('status', 'active')->where('expires_at', '<', $now);
        })->get();

        foreach ($users as $user) {
            $subscription = $user->subscriptions()->where('status', 'active')->where('expires_at', '<', $now)->first();

            if ($subscription) {
                $subscription->update(['status' => 'expired']);

                Mail::to($user->email)->send(new SubscriptionDeactivatedMail($user, $subscription));

                $this->info("Email enviado para {$user->email} e assinatura #{$subscription->id} desativada.");
            }
        }
    }
}
