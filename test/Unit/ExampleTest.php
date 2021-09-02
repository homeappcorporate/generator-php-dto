<?php

declare(strict_types=1);

namespace Test\Unit;

use Homeapp\LibraryOrProjectName\Example;
use PHPUnit\Framework\TestCase;

/**
 * @covers Example
 */
final class ExampleTest extends TestCase
{
    public function testVerifyClassIsWorkingAsExpected(): void
    {
        $class = new Example();
        self::assertSame('ok', $class->test());
    }
}
