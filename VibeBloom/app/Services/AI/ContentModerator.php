<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use OpenAI;

class ContentModerator
{
    public function review(string $text, string $contentType): array
    {
        $text = trim($text);
        if ($text === '') {
            return ['allowed' => true, 'reason' => null];
        }

        if ($reason = $this->localViolation($text)) {
            return ['allowed' => false, 'reason' => $reason];
        }

        $key = config('services.openai.key');
        if (!$key) {
            return ['allowed' => true, 'reason' => null];
        }

        try {
            $response = OpenAI::client($key)->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Moderas contenido de una comunidad de lugares. Rechaza amenazas, odio, acoso, contenido sexual explícito, datos personales, spam, promoción ilegal, insultos graves y texto incoherente o sin sentido (letras aleatorias, palabras inconexas o relleno que no comunica una experiencia u opinión). Acepta comentarios breves pero comprensibles y críticas respetuosas. Devuelve solo JSON: {"allowed":true|false,"reason":"explicación breve en español o null"}.'],
                    ['role' => 'user', 'content' => "Tipo: {$contentType}\nContenido:\n{$text}"],
                ],
                'temperature' => 0,
                'max_tokens' => 160,
            ]);

            $raw = (string) ($response->choices[0]->message->content ?? '{}');
            $raw = preg_replace('/^```(?:json)?|```$/im', '', trim($raw));
            $result = json_decode($raw, true);

            if (is_array($result) && array_key_exists('allowed', $result)) {
                return [
                    'allowed' => (bool) $result['allowed'],
                    'reason' => empty($result['reason']) ? null : (string) $result['reason'],
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('La moderación con IA no estuvo disponible.', ['message' => $e->getMessage()]);
        }

        return ['allowed' => true, 'reason' => null];
    }

    private function localViolation(string $text): ?string
    {
        if (preg_match('/(.)\1{5,}/u', $text) || preg_match('/\b(?:asdf+|qwer+|zxcv+|jaja(?:ja){5,})\b/iu', $text)) {
            return 'Escribe un comentario comprensible relacionado con tu experiencia en el lugar.';
        }

        if (preg_match('/https?:\/\/|www\./iu', $text)) {
            return 'No incluyas enlaces promocionales o externos.';
        }

        if (preg_match('/\b(?:puta|puto|pendej[oa]|idiota|mierda|cabron(?:a)?|ching(?:a|ar|ada))\b/iu', $text)) {
            return 'Usa un lenguaje respetuoso y elimina insultos o palabras ofensivas.';
        }

        return null;
    }
}
