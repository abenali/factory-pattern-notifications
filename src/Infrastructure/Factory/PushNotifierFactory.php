<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Notifier\NotifierInterface;
use App\Domain\ValueObject\NotificationChannel;
use App\Infrastructure\Notifier\PushNotifier;
use Psr\Log\LoggerInterface;

final class PushNotifierFactory extends AbstractNotifierFactory
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function create(): NotifierInterface
    {
        return new PushNotifier($this->logger);
    }

    public function supports(NotificationChannel $channel): bool
    {
        return NotificationChannel::PUSH === $channel;
    }
}
