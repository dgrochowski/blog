<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Category;
use App\Exception\SlugServiceException;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class SlugServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private SlugService $slugService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->slugService = new SlugService(
            slugger: new AsciiSlugger(),
            entityManager: $this->entityManager,
        );
    }

    /**
     * @return iterable<int, array{
     *     value: string,
     *     expected: string,
     * }>
     */
    public static function stringValueData(): iterable
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
            'value' => 'Jakiś polski tytuł',
            'expected' => 'jakis-polski-tytul',
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

    public function testUniqueSuccessfully(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects(self::exactly(2))
            ->method('findOneBy')
            ->willReturnOnConsecutiveCalls(
                new Category(),
                null,
            );

        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->with(Category::class)
            ->willReturn($repository);

        $uniqueSlug = $this->slugService->unique(Category::class, 'test-slug');

        $this->assertEquals(
            'test-slug-1',
            $uniqueSlug,
        );
    }

    public function testUniqueThrowsClassDoesNotExistException(): void
    {
        $this->expectException(SlugServiceException::class);
        $this->expectExceptionMessage('Class random-class-name does not exist');

        $this->slugService->unique('random-class-name', 'value');
    }

    public function testUniqueThrowsClassDoesNotImplementSlugEntityException(): void
    {
        $this->expectException(SlugServiceException::class);
        $this->expectExceptionMessage('Class '.self::class.' does not implement SlugEntity');

        $this->slugService->unique(self::class, 'value');
    }
}
