<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\CreateTagCommand;
use PHPUnit\Framework\TestCase;

final class CreateTagCommandTest extends TestCase
{
    /**
     * @return iterable<string, array{
     *     name: string,
     *     slug: string|null,
     * }>
     */
    public function tagData(): iterable
    {
        yield 'with name and slug' => [
            'name' => 'Test tag',
            'slug' => 'test-tag',
        ];

        yield 'with empty name and empty slug' => [
            'name' => '',
            'slug' => '',
        ];

        yield 'with name and nullable slug' => [
            'name' => 'Test tag',
            'slug' => null,
        ];
    }

    /**
     * @dataProvider tagData
     */
    public function testCreateTagCommand(string $name, ?string $slug): void
    {
        $command = new CreateTagCommand($name, $slug);

        $this->assertEquals($name, $command->name);
        $this->assertEquals($slug, $command->slug);
    }
}
