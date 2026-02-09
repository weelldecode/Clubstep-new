<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user->activeSubscription) {
            // Se não tiver assinatura ativa, redireciona ou retorna erro
            return redirect()
                ->route("home") // ou rota de assinatura
                ->with(
                    "error",
                    "Você precisa ter uma assinatura ativa para acessar essa área.",
                );
        }

        return $next($request);
    }
}
