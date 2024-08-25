<?php

namespace App\Http\Middleware;

use Closure;

class BasicAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $username = env('AUTH_USER', 'default_username');
        $password = env('AUTH_PASSWORD', 'default_password');

        if ($request->getUser() !== $username || $request->getPassword() !== $password) {
            return response('Unauthorized.', 401, ['WWW-Authenticate' => 'Basic']);
        }

        return $next($request);
    }
}

