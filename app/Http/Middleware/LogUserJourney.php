<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogUserJourney
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('get')) {
            $ip = $request->ip();
            $userAgent = $request->userAgent();
            $url = $request->fullUrl();
            $referrer = $request->header('referer', 'Direct Visit');

            $locationResponse = Http::get("http://ip-api.com/json/{$ip}");
            if ($locationResponse->successful()) {
                $locationData = $locationResponse->json();
                $country = $locationData['country'] ?? 'Unknown';
                $city = $locationData['city'] ?? 'Unknown';
            } else {
                $country = $city = 'Unknown';
            }

            Log::info("User Journey: $ip $country $city $url $userAgent $referrer");
        }

        return $next($request);
    }
}
