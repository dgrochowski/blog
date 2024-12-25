<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Exception\RandomStringGeneratorException;
use App\Service\RandomStringGenerator;
use PHPUnit\Framework\TestCase;

final class RandomStringGeneratorTest extends TestCase
{
    private RandomStringGenerator $randomStringGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->randomStringGenerator = new RandomStringGenerator();
    }

    public function testGenerateForLessThan1LengthException(): void
    {
        $this->expectException(RandomStringGeneratorException::class);
        $this->expectExceptionMessage('Length must be greater than 0');

        $this->randomStringGenerator->generate(0);
    }

    public function testGenerateForLength1(): void
    {
        $this->assertEquals(
            1,
            strlen($this->randomStringGenerator->generate(1)),
        );
    }

    public function testGenerateForLength15(): void
    {
        $this->assertEquals(
            15,
            strlen($this->randomStringGenerator->generate(15)),
        );
    }
}
