<?php

namespace App\Support;

use InvalidArgumentException;

class BannerTextFormatter
{
    /**
     * Expand variables and the supported numeric expressions in banner text.
     *
     * Supported expressions are `$(%A% - %B%)` and
     * `$format(%A%, "000")`. Invalid expressions are rendered as Unknown.
     *
     * @param  array<string, mixed>  $variables
     */
    public function format(string $text, array $variables): string
    {
        $text = preg_replace_callback(
            '/\$format\(\s*(%[A-Z0-9_?]+%)\s*,\s*"([0#,\.]+)"\s*\)/i',
            fn (array $matches): string => $this->formatVariable($matches[1], $matches[2], $variables),
            $text,
        ) ?? $text;

        $text = $this->replaceArithmeticExpressions($text, $variables);

        return preg_replace_callback(
            '/%[A-Z0-9_?]+%/i',
            fn (array $matches): string => $this->variableValue($matches[0], $variables) ?? 'Unknown',
            $text,
        ) ?? $text;
    }

    /** @param array<string, mixed> $variables */
    private function formatVariable(string $variable, string $pattern, array $variables): string
    {
        $value = $this->variableValue($variable, $variables);

        if ($value === null || ! is_numeric($value)) {
            return 'Unknown';
        }

        try {
            return $this->formatNumber((float) $value, $pattern);
        } catch (InvalidArgumentException) {
            return 'Unknown';
        }
    }

    /** @param array<string, mixed> $variables */
    private function replaceArithmeticExpressions(string $text, array $variables): string
    {
        $offset = 0;
        while (($start = strpos($text, '$(', $offset)) !== false) {
            $depth = 1;
            $end = $start + 2;
            $length = strlen($text);

            while ($end < $length && $depth > 0) {
                if ($text[$end] === '(') {
                    $depth++;
                } elseif ($text[$end] === ')') {
                    $depth--;
                }
                $end++;
            }

            if ($depth !== 0) {
                break;
            }

            $expression = substr($text, $start + 2, $end - $start - 3);
            $result = $this->evaluateExpression($expression, $variables);
            $text = substr_replace($text, $result, $start, $end - $start);
            $offset = $start + strlen($result);
        }

        return $text;
    }

    /** @param array<string, mixed> $variables */
    private function evaluateExpression(string $expression, array $variables): string
    {
        $expression = preg_replace_callback(
            '/%[A-Z0-9_?]+%/i',
            function (array $matches) use ($variables): string {
                $value = $this->variableValue($matches[0], $variables);

                return $value !== null && is_numeric($value) ? (string) $value : 'invalid';
            },
            $expression,
        ) ?? '';

        try {
            $parser = new BannerNumericExpressionParser($expression);
            $value = $parser->parse();

            return $this->numberToString($value);
        } catch (InvalidArgumentException) {
            return 'Unknown';
        }
    }

    /** @param array<string, mixed> $variables */
    private function variableValue(string $variable, array $variables): ?string
    {
        $name = strtoupper(trim($variable, '%'));

        return array_key_exists($name, $variables) ? (string) $variables[$name] : null;
    }

    private function formatNumber(float $number, string $pattern): string
    {
        if (substr_count($pattern, '.') > 1 || ! preg_match('/^[0#,\.]+$/', $pattern)) {
            throw new InvalidArgumentException('Invalid number format pattern.');
        }

        [$integerPattern, $fractionPattern] = array_pad(explode('.', $pattern, 2), 2, '');
        $minimumIntegerDigits = substr_count($integerPattern, '0');
        $minimumFractionDigits = substr_count($fractionPattern, '0');
        $maximumFractionDigits = strlen($fractionPattern);
        $absoluteNumber = abs($number);
        $rounded = number_format($absoluteNumber, $maximumFractionDigits, '.', '');
        [$integer, $fraction] = array_pad(explode('.', $rounded, 2), 2, '');

        $integer = str_pad($integer, $minimumIntegerDigits, '0', STR_PAD_LEFT);
        if (str_contains($integerPattern, ',')) {
            $integer = number_format((int) $integer, 0, '.', ',');
        }

        $fraction = rtrim($fraction, '0');
        $fraction = str_pad($fraction, $minimumFractionDigits, '0');

        return ($number < 0 ? '-' : '').$integer.($fraction === '' ? '' : '.'.$fraction);
    }

    private function numberToString(float $number): string
    {
        return rtrim(rtrim(sprintf('%.12F', $number), '0'), '.');
    }
}

/** @internal Parses the deliberately small arithmetic language used in banner text. */
class BannerNumericExpressionParser
{
    private int $position = 0;

    public function __construct(private readonly string $expression)
    {
    }

    public function parse(): float
    {
        $value = $this->parseSum();
        $this->skipWhitespace();

        if ($this->position !== strlen($this->expression) || ! is_finite($value)) {
            throw new InvalidArgumentException('Invalid numeric expression.');
        }

        return $value;
    }

    private function parseSum(): float
    {
        $value = $this->parseProduct();
        while (true) {
            $this->skipWhitespace();
            if ($this->consume('+')) {
                $value += $this->parseProduct();
            } elseif ($this->consume('-')) {
                $value -= $this->parseProduct();
            } else {
                return $value;
            }
        }
    }

    private function parseProduct(): float
    {
        $value = $this->parseValue();
        while (true) {
            $this->skipWhitespace();
            if ($this->consume('*')) {
                $value *= $this->parseValue();
            } elseif ($this->consume('/')) {
                $divisor = $this->parseValue();
                if ($divisor == 0.0) {
                    throw new InvalidArgumentException('Division by zero.');
                }
                $value /= $divisor;
            } else {
                return $value;
            }
        }
    }

    private function parseValue(): float
    {
        $this->skipWhitespace();
        if ($this->consume('+')) {
            return $this->parseValue();
        }
        if ($this->consume('-')) {
            return -$this->parseValue();
        }
        if ($this->consume('(')) {
            $value = $this->parseSum();
            $this->skipWhitespace();
            if (! $this->consume(')')) {
                throw new InvalidArgumentException('Unclosed parenthesis.');
            }

            return $value;
        }

        $remaining = substr($this->expression, $this->position);
        if (! preg_match('/^(?:\d+(?:\.\d*)?|\.\d+)/', $remaining, $matches)) {
            throw new InvalidArgumentException('Expected a number.');
        }
        $this->position += strlen($matches[0]);

        return (float) $matches[0];
    }

    private function consume(string $character): bool
    {
        if (($this->expression[$this->position] ?? null) !== $character) {
            return false;
        }
        $this->position++;

        return true;
    }

    private function skipWhitespace(): void
    {
        while (isset($this->expression[$this->position]) && ctype_space($this->expression[$this->position])) {
            $this->position++;
        }
    }
}
