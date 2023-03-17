<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    public function test_boolean_is_boolean()
    {
        $this->assertIsBool(true);
        $this->assertIsBool(false);
    }

    public function test_string_is_not_boolean()
    {
        $this->assertIsNotBool('true');
        $this->assertIsNotBool('false');
    }

    public function test_number_is_not_boolean()
    {
        $this->assertIsNotBool(0);
        $this->assertIsNotBool(1);
    }
}
