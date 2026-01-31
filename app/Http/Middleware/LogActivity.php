<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    protected array $hiddenKeys = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'access_token',
        'refresh_token',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            // Hindari logging file upload & binary
            $payload = $this->sanitizePayload(
                $request->except($this->hiddenKeys)
            );

            // Hindari logging response besar / file
            $responsePayload = null;
            if ($this->isJsonResponse($response)) {
                $responsePayload = $this->sanitizePayload(
                    $response->getData(true)
                );
            }

            ActivityLog::create([
                'user_id'          => auth()->id(),
                'method'           => $request->method(),
                'url'              => $request->fullUrl(),
                'status_code'      => $response->status(),
                'request_payload'  => $payload,
                'response_payload' => $responsePayload,
                'ip_address'       => $request->ip(),
                'user_agent'       => substr($request->userAgent(), 0, 1000),
            ]);
        } catch (\Throwable $e) {
            // ❌ Jangan ganggu request utama kalau logging gagal
            // Bisa ditambahkan log ke file jika perlu
        }

        return $response;
    }

    /**
     * Sanitize payload agar aman & tidak berat
     */
    protected function sanitizePayload(array $payload): array
    {
        // Remove file objects
        $payload = Arr::where($payload, function ($value) {
            return !is_resource($value);
        });

        // Limit nested depth (prevent huge payload)
        return $this->limitDepth($payload, 3);
    }

    /**
     * Batasi kedalaman array (anti payload raksasa)
     */
    protected function limitDepth(array $data, int $maxDepth, int $depth = 0): array
    {
        if ($depth >= $maxDepth) {
            return ['_truncated' => true];
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->limitDepth($value, $maxDepth, $depth + 1);
            }
        }

        return $data;
    }

    /**
     * Pastikan response JSON
     */
    protected function isJsonResponse($response): bool
    {
        return method_exists($response, 'getData');
    }
}
