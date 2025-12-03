<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

enum NotificationChannel: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH = 'push';
    case SLACK = 'slack';
}
