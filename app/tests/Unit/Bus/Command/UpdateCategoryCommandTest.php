<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\UpdateCategoryCommand;
use PHPUnit\Framework\TestCase;

final class UpdateCategoryCommandTest extends TestCase
{
    /**
     * @return iterable<string, array{
     *     name: string,
     *     oldSlug: string,
     *     newSlug: null|string,
     * }>
     */
    public function categoryData(): iterable
    {
        yield 'with name, old slug and new slug' => [
            'name' => 'Test category',
            'oldSlug' => 'test-category-1',
            'newSlug' => 'test-category',
        ];

        yield 'with empty name, empty old slug and empty new slug' => [
            'name' => '',
            'oldSlug' => '',
            'newSlug' => '',
        ];

        yield 'with name, old slug and nullable new slug' => [
            'name' => 'Test category',
            'oldSlug' => 'test-category',
            'newSlug' => null,
        ];
    }

    /**
     * @dataProvider categoryData
     */
    public function testCreateCategoryCommand(string $name, string $oldSlug, ?string $newSlug): void
    {
        $command = new UpdateCategoryCommand($name, $oldSlug, $newSlug);

        $this->assertEquals($name, $command->name);
        $this->assertEquals($oldSlug, $command->oldSlug);
        $this->assertEquals($newSlug, $command->newSlug);
    }
}
