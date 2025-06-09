<?php

namespace App\Http\Middleware;

use App\Services\EncryptionService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EncryptDecryptRequestResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        // if ($user && $request->has('encrypted')) {
        //     $decrypted = EncryptionService::decryptWithKey($request->input('encrypted'), $user->api_key);
        //     $request->merge($decrypted);
        // }

        if ($user && $request->has('encrypted')) {
            $decrypted = \App\Services\EncryptionService::decryptWithKey($request->input('encrypted'), $user->api_key);

            // Ensure decrypted data is array
            if (is_string($decrypted)) {
                $decrypted = json_decode($decrypted, true);
            }

            // Merge decrypted data into the request
            if (is_array($decrypted)) {
                $request->merge($decrypted);
            }
        }

        /** @var Response $response */
        $response = $next($request);

        if ($user && $response instanceof JsonResponse) {
            $data = $response->getData(true);
            $encrypted = EncryptionService::encryptWithKey($data, $user->api_key);
            $response->setData(['encrypted' => $encrypted]);
        }

        return $response;
    }
}
