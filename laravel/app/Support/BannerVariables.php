<?php

namespace App\Support;

class BannerVariables
{
    /**
     * Keep only values that can safely be used as named banner variables.
     *
     * @param array<array-key, mixed> $variables
     * @return array<string, scalar>
     */
    public static function sanitize(array $variables): array
    {
        $sanitized = [];

        foreach ($variables as $key => $value) {
            if (! is_string($key) || ! preg_match('/^[a-z][a-z0-9_]*$/i', $key)) {
                continue;
            }

            if ($value instanceof \Stringable) {
                $value = (string) $value;
            }

            if (! is_scalar($value)) {
                continue;
            }

            $sanitized[strtoupper($key)] = $value;
        }

        return $sanitized;
    }
}
