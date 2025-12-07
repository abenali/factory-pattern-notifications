<?php

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\ChannelSelector;
use App\Application\Service\NoAvailableChannelException;
use App\Domain\Entity\User;
use App\Domain\ValueObject\NotificationChannel;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ChannelSelectorTest extends TestCase
{
    private LoggerInterface $logger;
    private ChannelSelector $selector;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->selector = new ChannelSelector($this->logger);
    }

    public function testSelectsPreferredChannelWhenAvailable(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getPreferredChannel')->willReturn(NotificationChannel::EMAIL);
        $user->method('hasChannelAvailable')->with(NotificationChannel::EMAIL)->willReturn(true);

        $channel = $this->selector->selectChannel($user);

        $this->assertSame(NotificationChannel::EMAIL, $channel);
    }

    public function testSelectsFallbackChannelWhenPreferredIsNotAvailable(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getPreferredChannel')->willReturn(NotificationChannel::SLACK);

        $user->method('hasChannelAvailable')
            ->willReturnCallback(function (NotificationChannel $channel) {
                return match ($channel) {
                    NotificationChannel::SLACK => false,
                    NotificationChannel::PUSH => false,
                    NotificationChannel::EMAIL => true,
                    default => false,
                };
            });

        $channel = $this->selector->selectChannel($user);

        $this->assertSame(NotificationChannel::EMAIL, $channel);
    }

    public function testThrowsExceptionWhenNoChannelsAreAvailable(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getPreferredChannel')->willReturn(NotificationChannel::SLACK);
        $user->method('hasChannelAvailable')->willReturn(false);
        $user->method('getId')->willReturn('1');
        $user->method('isEmailVerified')->willReturn(false);
        $user->method('hasPhoneNumber')->willReturn(false);
        $user->method('hasPushToken')->willReturn(false);
        $user->method('isSlackConnected')->willReturn(false);

        $this->expectException(NoAvailableChannelException::class);
        $this->expectExceptionMessage('No available notification channel for user 1. Email verified: no, Has phone: no, Has push token: no, Has Slack: no');

        $this->selector->selectChannel($user);
    }

    public function testSelectsFirstAvailableFallbackChannel(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getPreferredChannel')->willReturn(NotificationChannel::SMS);
        $user->method('hasChannelAvailable')
            ->willReturnCallback(function (NotificationChannel $channel) {
                return match ($channel) {
                    NotificationChannel::SMS => false,
                    NotificationChannel::PUSH => true,
                    NotificationChannel::EMAIL => true,
                    default => false,
                };
            });

        $channel = $this->selector->selectChannel($user);

        $this->assertSame(NotificationChannel::PUSH, $channel);
    }
}
