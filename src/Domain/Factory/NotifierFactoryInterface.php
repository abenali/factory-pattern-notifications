<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Notifier\NotifierInterface;
use App\Domain\ValueObject\NotificationChannel;

interface NotifierFactoryInterface
{
    /**
     * Create a notifier instance.
     */
    public function create(): NotifierInterface;

    /**
     * Check if this factory supports the given channel.
     */
    public function supports(NotificationChannel $channel): bool;
}
