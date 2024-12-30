<?php

declare(strict_types=1);

namespace App\Tests\Unit\Bus;

use App\Bus\Bus;
use App\Bus\Command\CreateCategoryCommand;
use App\Bus\Command\CreateCategoryCommandHandler;
use App\Bus\Query\GetCategoryQuery;
use App\Bus\Query\GetCategoryQueryHandler;
use App\Entity\Category;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class BusTest extends TestCase
{
    private MessageBusInterface|MockObject $queryBus;
    private MessageBusInterface|MockObject $commandBus;
    private LoggerInterface|MockObject $logger;
    private Bus $bus;

    protected function setUp(): void
    {
        $this->queryBus = $this->createMock(MessageBusInterface::class);
        $this->commandBus = $this->createMock(MessageBusInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->bus = new Bus(
            queryBus: $this->queryBus,
            commandBus: $this->commandBus,
            logger: $this->logger,
        );
    }

    public function testQueryLogsErrorAndReturnsNullOnException(): void
    {
        $this->logger
            ->expects(self::once())
            ->method('error')
            ->with(sprintf(
                '%s error: %s',
                GetCategoryQuery::class,
                'Dispatch error',
            ));

        $query = new GetCategoryQuery('test-slug');

        $this->queryBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($query)
            ->willThrowException(new \Exception('Dispatch error'));

        $result = $this->bus->query($query);
        $this->assertNull($result);
    }

    public function testQueryLogsErrorAndReturnsNullWhenNoHandledStamp(): void
    {
        $this->logger
            ->expects(self::once())
            ->method('error')
            ->with(sprintf(
                'Query not handled correctly. Query: %s, contains: %s',
                GetCategoryQuery::class,
                '{"slug":"test-slug"}',
            ));

        $query = new GetCategoryQuery('test-slug');
        $envelope = new Envelope($query);

        $this->queryBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($query)
            ->willReturn($envelope);

        $result = $this->bus->query($query);
        $this->assertNull($result);
    }

    public function testQuery(): void
    {
        $this->logger
            ->expects(self::never())
            ->method('error');

        $category = new Category();
        $query = new GetCategoryQuery('test-slug');
        $envelope = new Envelope($query, [new HandledStamp($category, GetCategoryQueryHandler::class)]);

        $this->queryBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($query)
            ->willReturn($envelope);

        $result = $this->bus->query($query);
        $this->assertEquals($category, $result);
    }

    public function testCommandLogsErrorOnException(): void
    {
        $this->logger
            ->expects(self::once())
            ->method('error')
            ->with(sprintf(
                '%s error: %s',
                CreateCategoryCommand::class,
                'Dispatch error',
            ));

        $command = new CreateCategoryCommand('Test category');

        $this->commandBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($command)
            ->willThrowException(new \Exception('Dispatch error'));

        $this->bus->command($command);
    }

    public function testCommandLogsErrorWhenNoHandledStamp(): void
    {
        $this->logger
            ->expects(self::once())
            ->method('error')
            ->with(sprintf(
                'Command not handled correctly. Command: %s, contains: %s',
                CreateCategoryCommand::class,
                '{"name":"Test category","slug":null}',
            ));

        $command = new CreateCategoryCommand('Test category');
        $envelope = new Envelope($command);

        $this->commandBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($command)
            ->willReturn($envelope);

        $this->bus->command($command);
    }

    public function testCommand(): void
    {
        $this->logger
            ->expects(self::never())
            ->method('error');

        $category = new Category();
        $command = new CreateCategoryCommand('Test category');
        $envelope = new Envelope($command, [
            new HandledStamp($category, CreateCategoryCommandHandler::class),
        ]);

        $this->commandBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($command)
            ->willReturn($envelope);

        $this->bus->command($command);
    }
}
