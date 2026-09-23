<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\Helpers\SystemStatusController;
use App\Http\Controllers\Helpers\SystemStatusSeverity;
use PHPUnit\Framework\TestCase;

class SystemStatusSeverityTest extends TestCase
{
    public function test_it_reports_success_when_all_checks_are_operational(): void
    {
        $this->assertSame(SystemStatusSeverity::Success, $this->severityFor(SystemStatusSeverity::Success));
    }

    public function test_it_reports_warning_when_any_check_has_a_warning(): void
    {
        $this->assertSame(SystemStatusSeverity::Warning, $this->severityFor(SystemStatusSeverity::Warning));
    }

    public function test_it_reports_danger_when_any_check_has_an_error(): void
    {
        $this->assertSame(SystemStatusSeverity::Danger, $this->severityFor(SystemStatusSeverity::Danger));
    }

    private function severityFor(SystemStatusSeverity $severity): SystemStatusSeverity
    {
        $controller = new class(['CHECK' => ['severity' => $severity]]) extends SystemStatusController
        {
            /** @param array<string, array<string, SystemStatusSeverity>> $status */
            public function __construct(private array $status)
            {
            }

            public function system_status_json($optional_information = true): array
            {
                return $this->status;
            }
        };

        return $controller->overall_severity();
    }
}
