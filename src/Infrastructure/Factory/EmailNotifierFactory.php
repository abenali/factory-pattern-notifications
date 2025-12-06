<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Notifier\NotifierInterface;
use App\Domain\ValueObject\NotificationChannel;
use App\Infrastructure\Notifier\EmailNotifier;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;

final class EmailNotifierFactory extends AbstractNotifierFactory
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private string $fromEmail,
    ) {
    }

    public function create(): NotifierInterface
    {
        return new EmailNotifier($this->mailer, $this->logger, $this->fromEmail);
    }

    public function supports(NotificationChannel $channel): bool
    {
        return NotificationChannel::EMAIL === $channel;
    }
}
