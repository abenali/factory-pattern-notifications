<?php

declare(strict_types=1);

namespace App\Application\UseCase\SendNotification;

final readonly class SendNotificationCommand
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        public string $userId,
        public string $message,
        public array $metadata = [],
    ) {
    }
}
