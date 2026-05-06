<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

// FIX: Middleware ini belum ada sama sekali di project.
// Dibuat minimal agar tidak crash. Log ke file dulu (tidak butuh tabel DB).
class AuditLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Catat ke log file jika method yang relevan
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->log($request, $response);
        }

        return $response;
    }

    private function log(Request $request, Response $response): void
    {
        // Jangan catat request login/logout untuk hindari loop
        if (str_contains($request->path(), 'login') || str_contains($request->path(), 'logout')) {
            return;
        }

        Log::channel('daily')->info('AUDIT', [
            'user'   => auth()->id(),
            'method' => $request->method(),
            'path'   => $request->path(),
            'ip'     => $request->ip(),
            'status' => $response->getStatusCode(),
            'time'   => now()->toDateTimeString(),
        ]);
    }
}