<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\User;
use App\Domain\ValueObject\NotificationChannel;
use Psr\Log\LoggerInterface;

final class ChannelSelector
{
    /**
     * Fallback order if preferred channel is not available.
     */
    private const array FALLBACK_ORDER = [
        NotificationChannel::PUSH,
        NotificationChannel::EMAIL,
        NotificationChannel::SMS,
        NotificationChannel::SLACK,
    ];

    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Select the best available notification channel for a user.
     *
     * @throws NoAvailableChannelException if no channel is available
     */
    public function selectChannel(User $user): NotificationChannel
    {
        // 1. Try preferred channel first
        $preferredChannel = $user->getPreferredChannel();

        if ($user->hasChannelAvailable($preferredChannel)) {
            $this->logger->info('Using preferred channel', [
                'user_id' => $user->getId(),
                'channel' => $preferredChannel->value,
            ]);

            return $preferredChannel;
        }

        $this->logger->info('Preferred channel not available, trying fallback', [
            'user_id' => $user->getId(),
            'preferred_channel' => $preferredChannel->value,
        ]);

        // 2. Try fallback channels in order
        foreach (self::FALLBACK_ORDER as $channel) {
            if ($user->hasChannelAvailable($channel)) {
                $this->logger->info('Using fallback channel', [
                    'user_id' => $user->getId(),
                    'channel' => $channel->value,
                ]);

                return $channel;
            }
        }

        // 3. No channel available
        throw new NoAvailableChannelException($user);
    }
}
