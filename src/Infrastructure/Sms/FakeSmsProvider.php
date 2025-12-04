<?php

declare(strict_types=1);

namespace App\Infrastructure\Sms;

use Psr\Log\LoggerInterface;

/**
 * Fake SMS provider for development/testing.
 * Does not actually send SMS, just logs.
 */
final class FakeSmsProvider implements SmsProviderInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function sendSms(string $phoneNumber, string $message, string $apiKey): void
    {
        // Simulate API call delay
        usleep(100000); // 100ms

        // Simulate 95% success rate
        if (random_int(1, 100) <= 5) {
            throw new \RuntimeException('Simulated SMS provider failure');
        }

        $this->logger->info('[FAKE SMS] SMS sent', [
            'phone' => $phoneNumber,
            'message' => substr($message, 0, 50),
            'api_key_used' => substr($apiKey, 0, 10).'...',
        ]);
    }
}
