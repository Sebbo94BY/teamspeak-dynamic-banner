<?php

namespace Tests\Unit\Support;

use App\Support\BannerTextFormatter;
use PHPUnit\Framework\TestCase;

class BannerTextFormatterTest extends TestCase
{
    private BannerTextFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new BannerTextFormatter;
    }

    public function test_it_replaces_variables_case_insensitively(): void
    {
        $this->assertSame('Online: 12', $this->formatter->format('Online: %virtualserver_clientsonline%', ['VIRTUALSERVER_CLIENTSONLINE' => 12]));
    }

    public function test_it_calculates_arithmetic_expressions_with_variables(): void
    {
        $this->assertSame(
            'Online: 10 Users',
            $this->formatter->format('Online: $(%VIRTUALSERVER_CLIENTSONLINE% - %VIRTUALSERVER_QUERYCLIENTSONLINE%) Users', [
                'VIRTUALSERVER_CLIENTSONLINE' => '12',
                'VIRTUALSERVER_QUERYCLIENTSONLINE' => '2',
            ]),
        );
    }

    public function test_it_honours_operator_precedence_and_parentheses(): void
    {
        $this->assertSame('8 / 6', $this->formatter->format('$(2 + 3 * 2) / $((2 + 1) * 2)', []));
    }

    public function test_it_formats_numbers_with_required_and_optional_digits(): void
    {
        $this->assertSame('001 | 1.15 | 12,345.60', $this->formatter->format(
            '$format(%USERS%, "000") | $format(%RATIO%, "0.00###") | $format(%BYTES%, "#,##0.00")',
            ['USERS' => 1, 'RATIO' => '1.15', 'BYTES' => '12345.6'],
        ));
    }

    public function test_it_renders_invalid_expressions_and_non_numeric_format_values_as_unknown(): void
    {
        $this->assertSame('Unknown / Unknown / Unknown', $this->formatter->format(
            '$(%USERS% / 0) / $(%MISSING% + 1) / $format(%NAME%, "000")',
            ['USERS' => 10, 'NAME' => 'Alice'],
        ));
    }

    public function test_it_does_not_evaluate_arbitrary_input(): void
    {
        $this->assertSame('Unknown', $this->formatter->format('$(phpinfo())', []));
    }
}
