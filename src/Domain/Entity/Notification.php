<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\NotificationChannel;
use App\Domain\ValueObject\NotificationStatus;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'notifications')]
class Notification
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', enumType: NotificationChannel::class)]
    private NotificationChannel $channel;

    #[ORM\Column(type: 'text')]
    private string $message;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: 'json')]
    private array $metadata;

    #[ORM\Column(type: 'string', enumType: NotificationStatus::class)]
    private NotificationStatus $status;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $sentAt;

    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        User $user,
        NotificationChannel $channel,
        string $message,
        array $metadata,
        NotificationStatus $status,
        ?\DateTimeImmutable $sentAt = null,
        ?string $id = null,
    ) {
        $this->id = $id ?? Uuid::v4()->toRfc4122();
        $this->user = $user;
        $this->channel = $channel;
        $this->message = $message;
        $this->metadata = $metadata;
        $this->status = $status;
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
