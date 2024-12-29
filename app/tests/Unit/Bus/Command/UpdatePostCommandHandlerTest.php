<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\UpdatePostCommand;
use App\Bus\Command\UpdatePostCommandHandler;
use App\Entity\Category;
use App\Entity\File;
use App\Entity\Post;
use App\Entity\Tag;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdatePostCommandHandlerTest extends TestCase
{
    private PostRepository&MockObject $postRepository;
    private EntityManagerInterface&MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postRepository = $this->createMock(PostRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testUpdatePostCommandHandlerNothingToUpdate(): void
    {
        $this->postRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn(null);

        $this->entityManager->expects(self::never())
            ->method('persist');

        $command = new UpdatePostCommand(
            id: 321,
            name: 'Test Post',
            description: 'Test Post description',
            file: null,
            tags: [],
            category: null,
            slug: 'old-slug',
        );

        new UpdatePostCommandHandler(
            $this->postRepository,
            $this->entityManager,
        )($command);
    }

    public function testUpdatePostCommandHandler(): void
    {
        $tag1 = new Tag();
        $tag1->setName('Tag 1');
        $tag1->setSlug('tag2');
        $tag2 = new Tag();
        $tag2->setName('Tag 2');
        $tag2->setSlug('tag2');

        $category = new Category();
        $category->setName('Category');
        $category->setSlug('category');

        $file = new File();
        $file->setIsImage(true);
        $file->setFileName('file.jpg');
        $file->setOriginalName('originalName.jpg');
        $file->setMimeType('image/jpg');
        $file->setSize(1000);

        $post = new Post();
        $post->setName('Test Post');
        $post->setSlug('test-post');
        $post->setDescription('Test description');
        $post->setFile($file);
        $post->addTag($tag1);
        $post->addTag($tag2);
        $post->setCategory($category);

        $this->postRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn($post);

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($post);

        $command = new UpdatePostCommand(
            id: 321,
            name: 'Test Post',
            description: 'Test description',
            file: null,
            tags: [],
            category: null,
            slug: 'test-post',
        );

        new UpdatePostCommandHandler(
            $this->postRepository,
            $this->entityManager,
        )($command);
    }
}
