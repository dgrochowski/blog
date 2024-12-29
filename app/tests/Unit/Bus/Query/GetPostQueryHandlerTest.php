<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus\Query;

use App\Bus\Query\GetPostQuery;
use App\Bus\Query\GetPostQueryHandler;
use App\Entity\Category;
use App\Entity\File;
use App\Entity\Post;
use App\Entity\Tag;
use App\Repository\PostRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetPostQueryHandlerTest extends TestCase
{
    private PostRepository&MockObject $postRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postRepository = $this->createMock(PostRepository::class);
    }

    public function testGetTagQueryHandler(): void
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
            ->method('findOneBySlug')
            ->with('test-post')
            ->willReturn($post);

        $query = new GetPostQuery(
            slug: 'test-post',
        );

        $result = new GetPostQueryHandler($this->postRepository)($query);
        $this->assertEquals($post, $result);
    }
}
