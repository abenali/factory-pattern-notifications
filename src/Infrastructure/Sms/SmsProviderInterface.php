<?php

declare(strict_types=1);

namespace App\Infrastructure\Sms;

interface SmsProviderInterface
{
    /**
     * Send an SMS.
     *
     * @throws \RuntimeException if SMS fails to send
     */
    public function sendSms(string $phoneNumber, string $message, string $apiKey): void;
}
