<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\CreatePostCommand;
use App\Bus\Command\CreatePostCommandHandler;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreatePostCommandHandlerTest extends TestCase
{
    private SlugService&MockObject $slugService;
    private EntityManagerInterface&MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slugService = $this->createMock(SlugService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testCreatePostCommandHandler(): void
    {
        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-post');

        $tag1 = new Tag();
        $tag1->setName('Tag 1');
        $tag1->setSlug('tag2');
        $tag2 = new Tag();
        $tag2->setName('Tag 2');
        $tag2->setSlug('tag2');

        $category = new Category();
        $category->setName('Category');
        $category->setSlug('category');

        $post = new Post();
        $post->setName('Test Post');
        $post->setSlug('test-post');
        $post->setDescription('Test description');
        $post->setImageFilename('some-image-filename.jpg');
        $post->addTag($tag1);
        $post->addTag($tag2);
        $post->setCategory($category);

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($post);

        $command = new CreatePostCommand(
            name: 'Test Post',
            description: 'Test description',
            imageFilename: 'some-image-filename.jpg',
            tags: [$tag1, $tag2],
            category: $category,
            slug: 'test-post',
        );

        new CreatePostCommandHandler(
            $this->slugService,
            $this->entityManager,
        )($command);
    }

    public function testCreateTagCommandHandlerThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unable to persist');

        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-post');

        $tag1 = new Tag();
        $tag1->setName('Tag 1');
        $tag1->setSlug('tag2');
        $tag2 = new Tag();
        $tag2->setName('Tag 2');
        $tag2->setSlug('tag2');

        $category = new Category();
        $category->setName('Category');
        $category->setSlug('category');

        $post = new Post();
        $post->setName('Test Post');
        $post->setSlug('test-post');
        $post->setDescription('Test description');
        $post->setImageFilename('some-image-filename.jpg');
        $post->addTag($tag1);
        $post->addTag($tag2);
        $post->setCategory($category);

        $this->entityManager->expects(self::once())
            ->method('persist')
            ->willThrowException(new \Exception('unable to persist'));

        $command = new CreatePostCommand(
            name: 'Test Post',
            description: 'Test description',
            imageFilename: 'some-image-filename.jpg',
            tags: [$tag1, $tag2],
            category: $category,
            slug: 'test-post',
        );

        new CreatePostCommandHandler(
            $this->slugService,
            $this->entityManager,
        )($command);
    }
}
