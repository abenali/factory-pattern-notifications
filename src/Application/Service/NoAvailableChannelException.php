<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\User;

final class NoAvailableChannelException extends \RuntimeException
{
    public function __construct(User $user)
    {
        parent::__construct(
            sprintf(
                'No available notification channel for user %s. Email verified: %s, Has phone: %s, Has push token: %s, Has Slack: %s',
                $user->getId(),
                $user->isEmailVerified() ? 'yes' : 'no',
                $user->hasPhoneNumber() ? 'yes' : 'no',
                $user->hasPushToken() ? 'yes' : 'no',
                $user->isSlackConnected() ? 'yes' : 'no'
            )
        );
    }
}
