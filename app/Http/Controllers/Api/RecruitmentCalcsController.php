<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecruitmentCalcsController extends BaseApiController
{
    // GET /api/data/calculation
    public function calculation(Request $request)
    {
        $base = 'https://api1.inperson.com.br';
        $timeout = 10;

        try {
            // Busca Skills
            $skillsResp = Http::acceptJson()
                ->timeout($timeout)
                ->get("{$base}/profiles/skills");

            // Busca Adjectives
            $adjResp = Http::acceptJson()
                ->timeout($timeout)
                ->get("{$base}/profiles/adjectives");

            if ($skillsResp->failed() || $adjResp->failed()) {
                return response()->json([
                    'message' => 'Falha ao consultar serviços externos.',
                    'skills_status' => $skillsResp->status(),
                    'adjectives_status' => $adjResp->status(),
                ], 502);
            }

            $skills = collect(data_get($skillsResp->json(), 'data', []))
                ->map(fn ($it) => [
                    'id' => data_get($it, 'id'),
                    'description' => data_get($it, 'description'),
                ])->values();

            $adjectives = collect(data_get($adjResp->json(), 'data', []))
                ->map(fn ($it) => [
                    'id' => data_get($it, 'id'),
                    'description' => data_get($it, 'description'),
                ])->values();

            return response()->json([
                'skills' => $skills,
                'adjectives' => $adjectives,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro inesperado ao obter dados.',
                'error' => app()->isProduction() ? null : $e->getMessage(),
            ], 500);
        }
    }
}
