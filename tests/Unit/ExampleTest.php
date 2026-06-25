<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_basic_assertion(): void
    {
        $this->assertTrue(true);
    }

    public function test_math_operations(): void
    {
        $this->assertEquals(4, 2 + 2);
        $this->assertNotEquals(5, 2 + 2);
    }
}
