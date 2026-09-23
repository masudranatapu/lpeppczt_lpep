<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConfigureSessionCookieForCurrentHost
{
    /**
     * Allow the same codebase to run from localhost without losing its
     * Laravel session/CSRF cookie, while preserving the configured production
     * cookie settings for l-pep.org.
     */
    public function handle(Request $request, Closure $next)
    {
        $domain = config('session.domain');

        if ($domain && ! $this->hostMatchesDomain($request->getHost(), $domain)) {
            config([
                'session.domain' => null,
                'session.secure' => $request->isSecure(),
            ]);
        }

        return $next($request);
    }

    private function hostMatchesDomain(string $host, string $domain): bool
    {
        $domain = ltrim(strtolower($domain), '.');
        $host = strtolower($host);

        $subdomainSuffix = '.'.$domain;

        return $host === $domain
            || ($domain !== '' && substr($host, -strlen($subdomainSuffix)) === $subdomainSuffix);
    }
}
