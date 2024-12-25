<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Category;
use App\Service\RandomStringGenerator;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class SlugServiceTest extends TestCase
{
    private const DEFAULT_RANDOM_LENGTH = 8;
    private EntityManagerInterface&MockObject $entityManager;
    private SlugService $slugService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->slugService = new SlugService(
            slugger: new AsciiSlugger(),
            randomStringGenerator: new RandomStringGenerator(),
            entityManager: $this->entityManager,
            randomSlugLength: self::DEFAULT_RANDOM_LENGTH,
        );
    }

    /**
     * @return iterable<int, array{
     *     value: string,
     *     expected: string,
     * }>
     */
    public function stringValueData(): iterable
    {
        yield [
            'value' => 'Test',
            'expected' => 'test',
        ];
        yield [
            'value' => 'Test with spaces',
            'expected' => 'test-with-spaces',
        ];
        yield [
            'value' => '',
            'expected' => '',
        ];
    }

    /**
     * @dataProvider stringValueData
     */
    public function testGenerateFromString(string $value, string $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->slugService->generateFromString($value)
        );
    }

    public function testGenerateRandom(): void
    {
        $randomValue = $this->slugService->generateRandom(10);

        $this->assertEquals(
            strtolower(substr($randomValue, 0, 10)),
            $randomValue
        );
    }

    public function testGenerateRandomWithDefaultLength(): void
    {
        $randomValue = $this->slugService->generateRandom();

        $this->assertEquals(
            strtolower(substr($randomValue, 0, self::DEFAULT_RANDOM_LENGTH)),
            $randomValue
        );
    }

    public function testUnique(): void
    {
        $this->entityManager->expects(self::exactly(2))
            ->method('find')
            ->willReturnOnConsecutiveCalls(
                new Category(),
                null,
            );

        $uniqueSlug = $this->slugService->unique(new Category(), 'test-slug');

        $this->assertEquals(
            'test-slug-1',
            $uniqueSlug,
        );
    }
}
