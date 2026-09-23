<?php

namespace App\Support;

use InvalidArgumentException;

class BannerTextFormatter
{
    /**
     * Expand variables and the supported numeric expressions in banner text.
     *
     * Supported expressions are `$(%A% - %B%)`, `$format(%A%, "000")`,
     * `$if(%A% <= 20, "yes", "no")`, `$default(%NAME%, "Guest")`,
     * and `$ifset(%NAME%, "Hello %NAME%", "")`. Invalid expressions are
     * rendered as Unknown.
     *
     * @param  array<string, mixed>  $variables
     */
    public function format(string $text, array $variables): string
    {
        $text = $this->replaceConditionalFunctions($text, $variables);

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
    private function replaceConditionalFunctions(string $text, array $variables): string
    {
        while (preg_match('/\$(ifset|default|if)\(/i', $text, $match, PREG_OFFSET_CAPTURE)) {
            $name = strtolower($match[1][0]);
            $start = $match[0][1];
            $openingParenthesis = $start + strlen($match[0][0]) - 1;
            $end = $this->findClosingParenthesis($text, $openingParenthesis);

            if ($end === null) {
                return substr_replace($text, 'Unknown', $start, strlen($match[0][0]));
            }

            $arguments = $this->splitFunctionArguments(substr($text, $openingParenthesis + 1, $end - $openingParenthesis - 1));
            $replacement = $this->evaluateConditionalFunction($name, $arguments, $variables);
            $text = substr_replace($text, $replacement, $start, $end - $start + 1);
        }

        return $text;
    }

    private function findClosingParenthesis(string $text, int $openingParenthesis): ?int
    {
        $depth = 0;
        $quote = null;
        for ($position = $openingParenthesis; $position < strlen($text); $position++) {
            $character = $text[$position];
            if ($quote !== null) {
                if ($character === '\\' && $position + 1 < strlen($text)) {
                    $position++;
                } elseif ($character === $quote) {
                    $quote = null;
                }
                continue;
            }
            if ($character === '"' || $character === "'") {
                $quote = $character;
            } elseif ($character === '(') {
                $depth++;
            } elseif ($character === ')' && --$depth === 0) {
                return $position;
            }
        }

        return null;
    }

    /** @return array<int, string>|null */
    private function splitFunctionArguments(string $arguments): ?array
    {
        $result = [];
        $start = 0;
        $depth = 0;
        $quote = null;
        for ($position = 0; $position < strlen($arguments); $position++) {
            $character = $arguments[$position];
            if ($quote !== null) {
                if ($character === '\\' && $position + 1 < strlen($arguments)) {
                    $position++;
                } elseif ($character === $quote) {
                    $quote = null;
                }
                continue;
            }
            if ($character === '"' || $character === "'") {
                $quote = $character;
            } elseif ($character === '(') {
                $depth++;
            } elseif ($character === ')') {
                $depth--;
            } elseif ($character === ',' && $depth === 0) {
                $result[] = trim(substr($arguments, $start, $position - $start));
                $start = $position + 1;
            }
        }
        if ($quote !== null || $depth !== 0) {
            return null;
        }
        $result[] = trim(substr($arguments, $start));

        return $result;
    }

    /** @param array<int, string>|null $arguments
     *  @param array<string, mixed> $variables
     */
    private function evaluateConditionalFunction(string $name, ?array $arguments, array $variables): string
    {
        if ($arguments === null
            || ($name === 'if' && count($arguments) !== 3)
            || ($name === 'default' && count($arguments) !== 2)
            || ($name === 'ifset' && ! in_array(count($arguments), [2, 3], true))) {
            return 'Unknown';
        }

        if ($name === 'if') {
            $condition = $this->evaluateCondition($arguments[0], $variables);
            if ($condition === null) {
                return 'Unknown';
            }

            return $this->unquote($condition ? $arguments[1] : $arguments[2]);
        }

        if (! preg_match('/^%[A-Z0-9_?]+%$/i', $arguments[0])) {
            return 'Unknown';
        }
        $value = $this->variableValue($arguments[0], $variables);
        $isSet = $value !== null && trim($value) !== '';

        if ($name === 'default') {
            return $this->unquote($isSet ? $value : $arguments[1]);
        }

        return $this->unquote($isSet ? $arguments[1] : ($arguments[2] ?? ''));
    }

    /** @param array<string, mixed> $variables */
    private function evaluateCondition(string $condition, array $variables): ?bool
    {
        if (! preg_match('/^\s*(%[A-Z0-9_?]+%|-?(?:\d+(?:\.\d*)?|\.\d+))\s*(<=|>=|==|!=|<|>)\s*(%[A-Z0-9_?]+%|-?(?:\d+(?:\.\d*)?|\.\d+))\s*$/i', $condition, $matches)) {
            return null;
        }
        $left = $this->numericConditionValue($matches[1], $variables);
        $right = $this->numericConditionValue($matches[3], $variables);
        if ($left === null || $right === null) {
            return null;
        }

        return match ($matches[2]) {
            '<' => $left < $right,
            '<=' => $left <= $right,
            '>' => $left > $right,
            '>=' => $left >= $right,
            '==' => $left == $right,
            '!=' => $left != $right,
        };
    }

    /** @param array<string, mixed> $variables */
    private function numericConditionValue(string $value, array $variables): ?float
    {
        if (str_starts_with($value, '%')) {
            $value = $this->variableValue($value, $variables);
        }

        return $value !== null && is_numeric($value) ? (float) $value : null;
    }

    private function unquote(string $value): string
    {
        $value = trim($value);
        if (strlen($value) >= 2 && (($value[0] === '"' && $value[-1] === '"') || ($value[0] === "'" && $value[-1] === "'"))) {
            return stripcslashes(substr($value, 1, -1));
        }

        return $value;
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
