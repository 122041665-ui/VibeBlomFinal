<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RouteVoiceController extends Controller
{
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'max:8000'],
        ]);

        $apiKey = config('services.openai.key');

        if (!$apiKey) {
            return response()->json([
                'message' => 'Falta configurar OPENAI_API_KEY en el archivo .env'
            ], 500);
        }

        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->accept('audio/mpeg')
            ->post('https://api.openai.com/v1/audio/speech', [
                'model' => 'gpt-4o-mini-tts',
                'voice' => 'alloy',
                'input' => $validated['text'],
                'format' => 'mp3',
            ]);

        if (!$response->successful()) {
            Log::error('OpenAI TTS error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'message' => 'No fue posible generar el audio.',
                'status' => $response->status(),
                'error' => $response->json() ?: $response->body(),
            ], 500);
        }

        return response($response->body(), 200, [
            'Content-Type' => 'audio/mpeg',
            'Content-Disposition' => 'inline; filename="route.mp3"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}