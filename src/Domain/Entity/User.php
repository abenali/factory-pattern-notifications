<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\NotificationChannel;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(type: 'boolean')]
    private bool $emailVerified;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phone;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $pushToken;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $slackUserId;

    #[ORM\Column(type: 'string', enumType: NotificationChannel::class)]
    private NotificationChannel $preferredChannel;

    public function __construct(
        string $email,
        bool $emailVerified,
        ?string $phone,
        ?string $pushToken,
        ?string $slackUserId,
        NotificationChannel $preferredChannel,
        ?string $id = null,
    ) {
        $this->id = $id ?? Uuid::v4()->toRfc4122();
        $this->email = $email;
        $this->emailVerified = $emailVerified;
        $this->phone = $phone;
        $this->pushToken = $pushToken;
        $this->slackUserId = $slackUserId;
        $this->preferredChannel = $preferredChannel;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function hasPhoneNumber(): bool
    {
        return null !== $this->phone && '' !== $this->phone;
    }

    public function getPushToken(): ?string
    {
        return $this->pushToken;
    }

    public function hasPushToken(): bool
    {
        return null !== $this->pushToken && '' !== $this->pushToken;
    }

    public function getSlackUserId(): ?string
    {
        return $this->slackUserId;
    }

    public function isSlackConnected(): bool
    {
        return null !== $this->slackUserId && '' !== $this->slackUserId;
    }

    public function getPreferredChannel(): NotificationChannel
    {
        return $this->preferredChannel;
    }

    /**
     * Check if the user has the given channel available.
     */
    public function hasChannelAvailable(NotificationChannel $channel): bool
    {
        return match ($channel) {
            NotificationChannel::EMAIL => $this->isEmailVerified(),
            NotificationChannel::SMS => $this->hasPhoneNumber(),
            NotificationChannel::PUSH => $this->hasPushToken(),
            NotificationChannel::SLACK => $this->isSlackConnected(),
        };
    }
}
