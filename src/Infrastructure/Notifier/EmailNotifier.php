<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Domain\Notifier\NotifierInterface;
use App\Domain\ValueObject\NotificationChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class EmailNotifier implements NotifierInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private string $fromEmail = 'notifications@notifyhub.com',
    ) {
    }

    public function send(string $recipient, string $message, array $metadata = []): void
    {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($recipient)
                ->subject($metadata['subject'] ?? 'Notification')
                ->text($message);

            if (isset($metadata['html'])) {
                $email->html($metadata['html']);
            }

            $this->mailer->send($email);

            $this->logger->info('Email notification sent', [
                'channel' => 'email',
                'recipient' => $recipient,
                'subject' => $metadata['subject'] ?? 'Notification',
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to send email notification', [
                'recipient' => $recipient,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException(sprintf('Failed to send email to %s: %s', $recipient, $e->getMessage()), previous: $e);
        }
    }

    public function supports(NotificationChannel $channel): bool
    {
        return NotificationChannel::EMAIL === $channel;
    }

    public function getChannelName(): string
    {
        return NotificationChannel::EMAIL->value;
    }
}
