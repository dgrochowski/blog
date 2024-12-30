<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetFileQuery;
use App\Bus\Query\GetFileQueryHandler;
use App\Entity\File;
use App\Repository\FileRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetFileQueryHandlerTest extends TestCase
{
    private FileRepository|MockObject $fileRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileRepository = $this->createMock(FileRepository::class);
    }

    public function testGetFileQueryHandler(): void
    {
        $file = new File();
        $file->setSlug('test-slug');

        $this->fileRepository->expects(self::once())
            ->method('findOneBySlug')
            ->with('test-slug')
            ->willReturn($file);

        $query = new GetFileQuery(
            slug: 'test-slug',
        );

        $result = new GetFileQueryHandler($this->fileRepository)($query);
        $this->assertEquals($file, $result);
    }
}
