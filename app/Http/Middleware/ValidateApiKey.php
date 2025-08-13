<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Company;

class ValidateApiKey
{
    public function handle($request, Closure $next)
    {
        $apiKey = $request->header('api-key') ?? $request->header('Api-Key') ?? $request->header('X-API-KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'API Key não fornecida.'], 401);
        }

        $company = Company::where('api_key', $apiKey)
            ->where('is_active', 1)
            ->first();

        if (!$company) {
            return response()->json(['error' => 'API Key inválida ou empresa inativa.'], 403);
        }

        $request->merge(['company' => $company]);

        return $next($request);
    }
}
