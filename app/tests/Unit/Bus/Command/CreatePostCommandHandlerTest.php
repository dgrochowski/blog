<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Command;

use App\Bus\Command\CreatePostCommand;
use App\Bus\Command\CreatePostCommandHandler;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use App\Service\SlugService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreatePostCommandHandlerTest extends TestCase
{
    private SlugService|MockObject $slugService;
    private EntityManagerInterface|MockObject $entityManager;
    private CategoryRepository|MockObject $categoryRepository;
    private TagRepository|MockObject $tagRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slugService = $this->createMock(SlugService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->tagRepository = $this->createMock(TagRepository::class);
    }

    public function testCreatePostCommandHandlerSuccessfully(): void
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

        $commandCategory = $this->createMock(Category::class);
        $commandCategory->expects(self::once())
            ->method('getId')
            ->willReturn(1234);

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

        $this->tagRepository->expects(self::exactly(2))
            ->method('find')
            ->willReturnOnConsecutiveCalls($tag1, $tag2);
        $this->categoryRepository->expects(self::once())
            ->method('find')
            ->with(1234)
            ->willReturn($category);
        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($post);

        $command = new CreatePostCommand(
            name: 'Test Post',
            publishedAt: $publishedAt,
            description: 'Test description',
            uploadImageName: 'test-post.jpg',
            tags: new ArrayCollection([$tag1, $tag2]),
            category: $commandCategory,
            slug: 'test-post',
        );

        new CreatePostCommandHandler(
            $this->slugService,
            $this->entityManager,
            $this->categoryRepository,
            $this->tagRepository,
        )($command);

        $this->assertEquals($category, $post->getCategory());
        $this->assertEquals($tag1, $post->getTags()->get(0));
        $this->assertEquals($tag2, $post->getTags()->get(1));
        $this->assertEquals($post, $category->getPosts()->first());
    }

    public function testCreatePostCommandHandlerThrowsException(): void
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

        $commandCategory = $this->createMock(Category::class);
        $commandCategory->expects(self::once())
            ->method('getId')
            ->willReturn(1234);

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

        $this->tagRepository->expects(self::exactly(2))
            ->method('find')
            ->willReturnOnConsecutiveCalls($tag1, $tag2);
        $this->categoryRepository->expects(self::once())
            ->method('find')
            ->with(1234)
            ->willReturn($category);
        $this->entityManager->expects(self::once())
            ->method('persist')
            ->willThrowException(new \Exception('unable to persist'));

        $command = new CreatePostCommand(
            name: 'Test Post',
            publishedAt: $publishedAt,
            description: 'Test description',
            uploadImageName: 'test-post.jpg',
            tags: new ArrayCollection([$tag1, $tag2]),
            category: $commandCategory,
            slug: 'test-post',
        );

        new CreatePostCommandHandler(
            $this->slugService,
            $this->entityManager,
            $this->categoryRepository,
            $this->tagRepository,
        )($command);

        $this->assertEquals(0, $category->getPosts()->count());
    }

    public function testCreatePostCommandHandlerWithoutTagsAndCategory(): void
    {
        $this->slugService->expects(self::once())
            ->method('unique')
            ->willReturn('test-post');

        $publishedAt = new \DateTimeImmutable();
        $post = new Post();
        $post->setName('Test Post');
        $post->setSlug('test-post');
        $post->setDescription('Test description');
        $post->setUploadImageName('test-post.jpg');
        $post->setPublishedAt($publishedAt);

        $this->tagRepository->expects(self::never())
            ->method('find');
        $this->categoryRepository->expects(self::never())
            ->method('find');
        $this->entityManager->expects(self::once())
            ->method('persist')
            ->with($post);

        $command = new CreatePostCommand(
            name: 'Test Post',
            publishedAt: $publishedAt,
            description: 'Test description',
            uploadImageName: 'test-post.jpg',
            tags: new ArrayCollection(),
            category: null,
            slug: 'test-post',
        );

        new CreatePostCommandHandler(
            $this->slugService,
            $this->entityManager,
            $this->categoryRepository,
            $this->tagRepository,
        )($command);

        $this->assertEquals(null, $post->getCategory());
        $this->assertEquals(0, $post->getTags()->count());
    }
}
