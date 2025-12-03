<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\NotificationChannel;
use App\Domain\ValueObject\NotificationStatus;
use Symfony\Component\Uid\Uuid;

class Notification
{
    private string $id;
    private \DateTimeImmutable $sentAt;

    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private User $user,
        private NotificationChannel $channel,
        private string $message,
        private array $metadata,
        private NotificationStatus $status,
        ?\DateTimeImmutable $sentAt = null,
        ?string $id = null,
    ) {
        $this->id = $id ?? Uuid::v4()->toRfc4122();
        $this->sentAt = $sentAt ?? new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getChannel(): NotificationChannel
    {
        return $this->channel;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getStatus(): NotificationStatus
    {
        return $this->status;
    }

    public function getSentAt(): \DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function markAsSent(): void
    {
        $this->status = NotificationStatus::SENT;
        $this->sentAt = new \DateTimeImmutable();
    }

    public function markAsFailed(): void
    {
        $this->status = NotificationStatus::FAILED;
    }
}
