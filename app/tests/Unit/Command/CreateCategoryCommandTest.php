<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\CreateCategoryCommand;
use PHPUnit\Framework\TestCase;

final class CreateCategoryCommandTest extends TestCase
{
    /**
     * @return iterable<string, array{
     *     name: string,
     *     slug: string|null,
     * }>
     */
    public function categoryData(): iterable
    {
        yield 'with name and slug' => [
            'name' => 'Test category',
            'slug' => 'test-category',
        ];

        yield 'with empty name and empty slug' => [
            'name' => '',
            'slug' => '',
        ];

        yield 'with name and nullable slug' => [
            'name' => 'Test category',
            'slug' => null,
        ];
    }

    /**
     * @dataProvider categoryData
     */
    public function testCreateCategoryCommand(string $name, ?string $slug): void
    {
        $command = new CreateCategoryCommand($name, $slug);

        $this->assertEquals($name, $command->getName());
        $this->assertEquals($slug, $command->getSlug());
    }
}
