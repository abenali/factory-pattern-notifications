<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Domain\Notifier\NotifierInterface;
use App\Domain\ValueObject\NotificationChannel;
use Psr\Log\LoggerInterface;

final class PushNotifier implements NotifierInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function send(string $recipient, string $message, array $metadata = []): void
    {
        try {
            // Simulate Firebase Cloud Messaging / APNS call
            $priority = $metadata['priority'] ?? 'normal';
            $title = $metadata['title'] ?? 'Notification';

            // In real implementation, you would call FCM/APNS API here
            // For now, we just log
            usleep(50000); // Simulate API call

            $this->logger->info('Push notification sent', [
                'channel' => NotificationChannel::PUSH->value,
                'token' => substr($recipient, 0, 20).'...',
                'title' => $title,
                'priority' => $priority,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to send push notification', [
                'token' => substr($recipient, 0, 20).'...',
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException(sprintf('Failed to send push notification: %s', $e->getMessage()), previous: $e);
        }
    }

    public function supports(NotificationChannel $channel): bool
    {
        return NotificationChannel::PUSH === $channel;
    }

    public function getChannelName(): string
    {
        return NotificationChannel::PUSH->value;
    }
}
