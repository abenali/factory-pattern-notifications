<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\NotificationChannel;
use Symfony\Component\Uid\Uuid;

class User
{
    private string $id;

    public function __construct(
        private string $email,
        private bool $emailVerified,
        private ?string $phone,
        private ?string $pushToken,
        private ?string $slackUserId,
        private NotificationChannel $preferredChannel,
        ?string $id = null,
    ) {
        $this->id = $id ?? Uuid::v4()->toRfc4122();
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
