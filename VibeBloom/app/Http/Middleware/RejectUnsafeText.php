<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RejectUnsafeText
{
    private const EXCLUDED = ['_token', 'password', 'password_confirmation', 'current_password', 'token'];

    public function handle(Request $request, Closure $next)
    {
        $errors = [];
        $this->inspect($request->except(self::EXCLUDED), '', $errors);

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $next($request);
    }

    private function inspect(array $values, string $prefix, array &$errors): void
    {
        foreach ($values as $key => $value) {
            $field = ltrim($prefix.'.'.$key, '.');
            if (is_array($value)) {
                $this->inspect($value, $field, $errors);
                continue;
            }

            if (!is_string($value) || $value === '') {
                continue;
            }

            if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', $value) || preg_match('/<\s*\/?\s*[a-z][^>]*>/iu', $value)) {
                $errors[$field] = 'El campo contiene caracteres o etiquetas no permitidos.';
            }
        }
    }
}
