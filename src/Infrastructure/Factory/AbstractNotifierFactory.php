<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Factory\NotifierFactoryInterface;

/**
 * Abstract base class for notifier factories.
 * Implements the Factory Method Pattern.
 */
abstract class AbstractNotifierFactory implements NotifierFactoryInterface
{
    // Subclasses must implement:
    // - create(): NotifierInterface
    // - supports(NotificationChannel $channel): bool
}
