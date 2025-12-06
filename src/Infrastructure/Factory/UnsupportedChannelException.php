<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\ValueObject\NotificationChannel;

final class UnsupportedChannelException extends \RuntimeException
{
    public function __construct(NotificationChannel $channel)
    {
        parent::__construct(
            sprintf('No factory found for notification channel: %s', $channel->value)
        );
    }
}
