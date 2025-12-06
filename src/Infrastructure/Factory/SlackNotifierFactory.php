<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Notifier\NotifierInterface;
use App\Domain\ValueObject\NotificationChannel;
use App\Infrastructure\Notifier\SlackNotifier;
use Psr\Log\LoggerInterface;

final class SlackNotifierFactory extends AbstractNotifierFactory
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function create(): NotifierInterface
    {
        return new SlackNotifier($this->logger);
    }

    public function supports(NotificationChannel $channel): bool
    {
        return NotificationChannel::SLACK === $channel;
    }
}
