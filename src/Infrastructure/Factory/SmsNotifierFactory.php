<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Notifier\NotifierInterface;
use App\Domain\ValueObject\NotificationChannel;
use App\Infrastructure\Notifier\SmsNotifier;
use App\Infrastructure\Sms\SmsProviderInterface;
use Psr\Log\LoggerInterface;

final class SmsNotifierFactory extends AbstractNotifierFactory
{
    public function __construct(
        private SmsProviderInterface $smsProvider,
        private LoggerInterface $logger,
        private string $smsApiKey,
    ) {
    }

    public function create(): NotifierInterface
    {
        return new SmsNotifier($this->smsProvider, $this->logger, $this->smsApiKey);
    }

    public function supports(NotificationChannel $channel): bool
    {
        return NotificationChannel::SMS === $channel;
    }
}
