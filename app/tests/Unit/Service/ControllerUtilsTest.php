<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Bus\Command\CreateTagCommand;
use App\Entity\Tag;
use App\Exception\ControllerUtilsException;
use App\Exception\SlugServiceException;
use App\Repository\TagRepository;
use App\Service\ControllerUtils;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ControllerUtilsTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private ControllerUtils $controllerUtils;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->controllerUtils = new ControllerUtils($this->entityManager);
    }

    public function testIsSlugTaken(): void
    {
        $tag = new Tag();
        $tag->setName('Test name');
        $tag->setSlug('test-slug');

        $repository = $this->createMock(TagRepository::class);
        $repository->expects(self::once())
            ->method('findOneBy')
            ->with(['slug' => 'test-slug'])
            ->willReturn(null);

        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->with(Tag::class)
            ->willReturn($repository);

        $result = $this->controllerUtils->isSlugTaken($tag);
        $this->assertFalse($result);
    }

    /**
     * @return iterable<string, array{
     *     fields: string[],
     *     class: string,
     * }>
     */
    public static function sluggableData(): iterable
    {
        yield 'sluggable data' => [
            'fields' => ['id', 'name', 'slug'],
            'class' => Tag::class,
            'expected' => true,
        ];

        yield 'sluggable class without slug field' => [
            'fields' => [],
            'class' => Tag::class,
            'expected' => false,
        ];

        yield 'not sluggable class with slug field' => [
            'fields' => ['slug'],
            'class' => self::class,
            'expected' => false,
        ];

        yield 'not sluggable class without slug field' => [
            'fields' => [],
            'class' => self::class,
            'expected' => false,
        ];
    }

    /**
     * @param string[] $fields
     *
     * @dataProvider sluggableData
     */
    public function testIsSluggable(array $fields, string $class, bool $expected): void
    {
        $this->assertEquals($expected, $this->controllerUtils->isSluggable($fields, $class));
    }

    public function testBuildCommand(): void
    {
        $tag = new Tag();
        $tag->setName('Test Tag');

        $expectedCommand = new CreateTagCommand(name: 'Test Tag');
        $result = $this->controllerUtils->buildCommand(
            prefix: 'Create',
            entityInstance: $tag,
            fields: ['name'],
        );
        $this->assertEquals($expectedCommand, $result);
    }

    public function testBuildCommandFromNotExistingClass(): void
    {
        $this->expectException(ControllerUtilsException::class);
        $this->expectExceptionMessage('Class "App\Bus\Command\CheckSlugServiceExceptionCommand" not found');

        $entityInstance = new SlugServiceException();

        $this->controllerUtils->buildCommand(
            prefix: 'Check',
            entityInstance: $entityInstance,
            fields: [],
        );
    }

    public function testBuildCommandNotExistingMethod(): void
    {
        $this->expectException(ControllerUtilsException::class);
        $this->expectExceptionMessage('Method "getSome-not-existing-method" not found in class "'.Tag::class.'"');

        $tag = new Tag();
        $tag->setName('Test Tag');

        $this->controllerUtils->buildCommand(
            prefix: 'Create',
            entityInstance: $tag,
            fields: ['name', 'some-not-existing-method'],
        );
    }
}
