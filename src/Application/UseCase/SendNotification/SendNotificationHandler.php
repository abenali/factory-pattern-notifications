<?php

declare(strict_types=1);

namespace App\Application\UseCase\SendNotification;

use App\Application\Service\ChannelSelector;
use App\Domain\Entity\Notification;
use App\Domain\Entity\User;
use App\Domain\Repository\NotificationRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\ValueObject\NotificationChannel;
use App\Domain\ValueObject\NotificationStatus;
use App\Infrastructure\Factory\NotifierFactoryRegistry;
use Psr\Log\LoggerInterface;

final class SendNotificationHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private NotificationRepositoryInterface $notificationRepository,
        private ChannelSelector $channelSelector,
        private NotifierFactoryRegistry $factoryRegistry,
        private LoggerInterface $logger,
    ) {
    }

    public function execute(SendNotificationCommand $command): SendNotificationResponse
    {
        // 1. Retrieve user
        $user = $this->userRepository->findById($command->userId);

        // 2. Select best channel
        $channel = $this->channelSelector->selectChannel($user);

        // 3. Get recipient based on channel
        $recipient = $this->getRecipient($user, $channel);

        // 4. Create notification entity
        $notification = new Notification(
            user: $user,
            channel: $channel,
            message: $command->message,
            metadata: $command->metadata,
            status: NotificationStatus::PENDING
        );

        try {
            // 5. Get factory and create notifier
            $factory = $this->factoryRegistry->getFactory($channel);
            $notifier = $factory->create();

            // 6. Send notification
            $notifier->send($recipient, $command->message, $command->metadata);

            // 7. Mark as sent
            $notification->markAsSent();

            $this->logger->info('Notification sent successfully', [
                'notification_id' => $notification->getId(),
                'user_id' => $user->getId(),
                'channel' => $channel->value,
            ]);
        } catch (\Throwable $e) {
            // 8. Mark as failed
            $notification->markAsFailed();

            $this->logger->error('Notification failed to send', [
                'notification_id' => $notification->getId(),
                'user_id' => $user->getId(),
                'channel' => $channel->value,
                'error' => $e->getMessage(),
            ]);

            // Persist failed notification and re-throw
            $this->notificationRepository->save($notification);
            throw $e;
        }

        // 9. Persist notification
        $this->notificationRepository->save($notification);

        // 10. Return response
        return new SendNotificationResponse(
            notificationId: $notification->getId(),
            channel: $notification->getChannel(),
            status: $notification->getStatus(),
            sentAt: $notification->getSentAt()
        );
    }

    private function getRecipient(User $user, NotificationChannel $channel): string
    {
        return match ($channel) {
            NotificationChannel::EMAIL => $user->getEmail(),
            NotificationChannel::SMS => $user->getPhone() ?? throw new \RuntimeException('No phone number'),
            NotificationChannel::PUSH => $user->getPushToken() ?? throw new \RuntimeException('No push token'),
            NotificationChannel::SLACK => $user->getSlackUserId() ?? throw new \RuntimeException('No Slack user ID'),
        };
    }
}
