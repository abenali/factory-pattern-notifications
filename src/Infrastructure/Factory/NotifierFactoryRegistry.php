<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Factory\NotifierFactoryInterface;
use App\Domain\ValueObject\NotificationChannel;

final class NotifierFactoryRegistry
{
    /**
     * @param iterable<NotifierFactoryInterface> $factories
     */
    public function __construct(
        private iterable $factories,
    ) {
    }

    /**
     * Get the factory that supports the given channel.
     *
     * @throws UnsupportedChannelException
     */
    public function getFactory(NotificationChannel $channel): NotifierFactoryInterface
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($channel)) {
                return $factory;
            }
        }

        throw new UnsupportedChannelException($channel);
    }
}
