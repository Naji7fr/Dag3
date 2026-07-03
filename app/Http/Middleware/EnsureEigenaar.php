<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Beperkt toegang tot routes voor de rol eigenaar.
 */
class EnsureEigenaar
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isEigenaar()) {
            abort(403, 'Alleen de eigenaar heeft toegang tot deze pagina.');
        }

        return $next($request);
    }
}
