<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\UpdatePostCommand;
use App\Bus\Command\UpdatePostCommandHandler;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdatePostCommandHandlerTest extends TestCase
{
    private PostRepository|MockObject $postRepository;
    private CategoryRepository|MockObject $categoryRepository;
    private TagRepository|MockObject $tagRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postRepository = $this->createMock(PostRepository::class);
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->tagRepository = $this->createMock(TagRepository::class);
    }

    public function testUpdatePostCommandHandlerNothingToUpdate(): void
    {
        $this->postRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn(null);

        $command = new UpdatePostCommand(
            id: 321,
            name: 'Test Post',
            publishedAt: new \DateTimeImmutable(),
            description: 'Test Post description',
            uploadImageName: null,
            tags: new ArrayCollection(),
            category: null,
            slug: 'old-slug',
        );

        new UpdatePostCommandHandler(
            $this->postRepository,
            $this->categoryRepository,
            $this->tagRepository,
        )($command);
    }

    public function testUpdatePostCommandHandlerSuccessfully(): void
    {
        $tag1 = new Tag();
        $tag1->setName('Tag 1');
        $tag1->setSlug('tag2');
        $tag2 = new Tag();
        $tag2->setName('Tag 2');
        $tag2->setSlug('tag2');

        $commandCategory = $this->createMock(Category::class);
        $commandCategory->expects(self::once())
            ->method('getId')
            ->willReturn(4321);

        $category = new Category();
        $category->setName('Category');
        $category->setSlug('category');

        $publishedAt = new \DateTimeImmutable();
        $post = new Post();
        $post->setName('Test Post');
        $post->setSlug('test-post');
        $post->setDescription('Test description');
        $post->setUploadImageName('test-post.jpg');
        $post->addTag($tag1);
        $post->addTag($tag2);
        $post->setCategory($category);
        $post->setPublishedAt($publishedAt);

        $this->postRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn($post);
        $this->tagRepository->expects(self::exactly(2))
            ->method('find')
            ->willReturnOnConsecutiveCalls($tag1, $tag2);
        $this->categoryRepository->expects(self::once())
            ->method('find')
            ->with(4321)
            ->willReturn($category);

        $command = new UpdatePostCommand(
            id: 321,
            name: 'Test Post',
            publishedAt: $publishedAt,
            description: 'Test description',
            uploadImageName: 'test-post.jpg',
            tags: new ArrayCollection([$tag1, $tag2]),
            category: $commandCategory,
            slug: 'test-post',
        );

        new UpdatePostCommandHandler(
            $this->postRepository,
            $this->categoryRepository,
            $this->tagRepository,
        )($command);
    }

    public function testUpdatePostCommandHandlerWithoutTagsAndCategory(): void
    {
        $post = new Post();
        $post->setName('Test Post');
        $post->setSlug('test-post');
        $post->setDescription('Test description');
        $post->setUploadImageName('test-post.jpg');

        $this->postRepository->expects(self::once())
            ->method('find')
            ->with(321)
            ->willReturn($post);
        $this->tagRepository->expects(self::never())
            ->method('find');
        $this->categoryRepository->expects(self::never())
            ->method('find');

        $command = new UpdatePostCommand(
            id: 321,
            name: 'Test Post',
            publishedAt: new \DateTimeImmutable(),
            description: 'Test description',
            uploadImageName: 'test-post.jpg',
            tags: new ArrayCollection(),
            category: null,
            slug: 'test-post',
        );

        new UpdatePostCommandHandler(
            $this->postRepository,
            $this->categoryRepository,
            $this->tagRepository,
        )($command);
    }
}
