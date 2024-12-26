<?php

declare(strict_types=1);

namespace App\Service;

use App\Command\CommandInterface;
use App\Query\QueryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class Bus
{
    public function __construct(
        private MessageBusInterface $queryBus,
        private MessageBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    public function query(QueryInterface $query): mixed
    {
        try {
            $envelope = $this->queryBus->dispatch($query);
        } catch (\Throwable $exception) {
            $this->logger->error(sprintf(
                '%s error: %s',
                $query::class,
                $exception->getMessage(),
            ));

            return null;
        }
        $handledStamp = $envelope->last(HandledStamp::class);

        if (null === $handledStamp) {
            $this->logger->error(sprintf(
                'Query not handled correctly. Query: %s, contains: %s',
                $query::class,
                \json_encode($query),
            ));

            return null;
        }

        return $handledStamp->getResult();
    }

    public function command(CommandInterface $command): void
    {
        try {
            $envelope = $this->commandBus->dispatch($command);
        } catch (\Throwable $exception) {
            $this->logger->error(sprintf(
                '%s error: %s',
                $command::class,
                $exception->getMessage(),
            ));

            return;
        }
        $handledStamp = $envelope->last(HandledStamp::class);

        if (null === $handledStamp) {
            $this->logger->error(sprintf(
                'Command not handled correctly. Command: %s, contains: %s',
                $command::class,
                \json_encode($command),
            ));
        }
    }
}
