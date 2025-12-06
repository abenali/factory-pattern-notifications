<?php

declare(strict_types=1);

namespace App\Application\UseCase\SendNotification;

use App\Domain\ValueObject\NotificationChannel;
use App\Domain\ValueObject\NotificationStatus;

final readonly class SendNotificationResponse
{
    public function __construct(
        public string $notificationId,
        public NotificationChannel $channel,
        public NotificationStatus $status,
        public \DateTimeImmutable $sentAt,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'notificationId' => $this->notificationId,
            'channel' => $this->channel->value,
            'status' => $this->status->value,
            'sentAt' => $this->sentAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
