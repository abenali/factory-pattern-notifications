<?php

declare(strict_types=1);

namespace App\Domain\Notifier;

use App\Domain\ValueObject\NotificationChannel;

interface NotifierInterface
{
    /**
     * Send a notification to the recipient.
     *
     * @param string               $recipient The recipient identifier (email, phone, token, user ID)
     * @param string               $message   The message to send
     * @param array<string, mixed> $metadata  Additional metadata (subject, priority, etc.)
     *
     * @throws \RuntimeException if the notification fails to send
     */
    public function send(string $recipient, string $message, array $metadata = []): void;

    /**
     * Check if this notifier supports the given channel.
     */
    public function supports(NotificationChannel $channel): bool;

    /**
     * Get the channel name for logging/display purposes.
     */
    public function getChannelName(): string;
}
