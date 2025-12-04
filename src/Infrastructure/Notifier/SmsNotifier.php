<?php

namespace App\Infrastructure\Notifier;

use App\Domain\Notifier\NotifierInterface;
use App\Domain\ValueObject\NotificationChannel;
use App\Infrastructure\Sms\SmsProviderInterface;
use Psr\Log\LoggerInterface;

class SmsNotifier implements NotifierInterface
{
    public function __construct(
        private SmsProviderInterface $smsProvider,
        private LoggerInterface $logger,
        private string $smsApiKey,
    ) {
    }

    public function send(string $recipient, string $message, array $metadata = []): void
    {
        try {
            $this->smsProvider->sendSms($recipient, $message, $this->smsApiKey);

            $this->logger->info('SMS notification sent', [
                'channel' => 'sms',
                'recipient' => $recipient,
                'message_length' => strlen($message),
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to send sms notification', [
                'recipient' => $recipient,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException(sprintf('Failed to send sms to %s: %s', $recipient, $e->getMessage()), previous: $e);
        }
    }

    public function supports(NotificationChannel $channel): bool
    {
        return NotificationChannel::SMS === $channel;
    }

    public function getChannelName(): string
    {
        return NotificationChannel::SMS->value;
    }
}
