<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockMaliciousRequests
{
    /**
     * Block requests targeting known exploit paths.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();

        $blockedPatterns = [
            'wp-includes',
            'wp-admin',
            'wp-content',
            'wp-login',
            'xmlrpc.php',
            '.env',
            'phpmyadmin',
            'phpMyAdmin',
        ];

        foreach ($blockedPatterns as $pattern) {
            if (str_contains($path, $pattern)) {
                abort(403);
            }
        }

        return $next($request);
    }
}
