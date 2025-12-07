<?php

namespace App\Tests\Unit\Infrastructure\Factory;

use App\Domain\ValueObject\NotificationChannel;
use App\Infrastructure\Factory\EmailNotifierFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;

class EmailNotifierFactoryTest extends TestCase
{
    private LoggerInterface $logger;
    private MailerInterface $mailer;

    private EmailNotifierFactory $factory;

    public function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->factory = new EmailNotifierFactory($this->mailer, $this->logger, 'from@example.com');
    }

    public function testSupportsEmailChannel(): void
    {
        $this->assertTrue($this->factory->supports(NotificationChannel::EMAIL));
    }

    public function testDoesNotSupportOtherChannels(): void
    {
        $this->assertFalse($this->factory->supports(NotificationChannel::SMS));
        $this->assertFalse($this->factory->supports(NotificationChannel::SLACK));
        $this->assertFalse($this->factory->supports(NotificationChannel::PUSH));
    }

    public function testCreateEmailNotifier(): void
    {
        $notifier = $this->factory->create();
        $this->assertSame('email', $notifier->getChannelName());
    }
}
