<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Domain\Notifier\NotifierInterface;
use App\Domain\ValueObject\NotificationChannel;
use Psr\Log\LoggerInterface;

final class SlackNotifier implements NotifierInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function send(string $recipient, string $message, array $metadata = []): void
    {
        try {
            // Simulate Slack API call
            $channel = $metadata['slack_channel'] ?? '#general';

            // In real implementation, you would call Slack Web API here
            // POST https://slack.com/api/chat.postMessage
            usleep(80000); // Simulate API call

            $this->logger->info('Slack notification sent', [
                'channel' => NotificationChannel::SLACK->value,
                'user_id' => $recipient,
                'slack_channel' => $channel,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to send Slack notification', [
                'user_id' => $recipient,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException(sprintf('Failed to send Slack notification to %s: %s', $recipient, $e->getMessage()), previous: $e);
        }
    }

    public function supports(NotificationChannel $channel): bool
    {
        return NotificationChannel::SLACK === $channel;
    }

    public function getChannelName(): string
    {
        return NotificationChannel::SLACK->value;
    }
}
