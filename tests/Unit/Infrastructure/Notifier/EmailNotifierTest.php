<?php

namespace App\Tests\Unit\Infrastructure\Notifier;

use App\Domain\ValueObject\NotificationChannel;
use App\Infrastructure\Notifier\EmailNotifier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotifierTest extends TestCase
{
    private MailerInterface&MockObject $mailer;
    private LoggerInterface&MockObject $logger;
    private EmailNotifier $notifier;

    protected function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->notifier = new EmailNotifier($this->mailer, $this->logger, 'from@example.com');
    }

    public function testSupportsEmailChannel(): void
    {
        $this->assertTrue($this->notifier->supports(NotificationChannel::EMAIL));
    }

    public function testDoesNotSupportOtherChannels(): void
    {
        $this->assertFalse($this->notifier->supports(NotificationChannel::SMS));
        $this->assertFalse($this->notifier->supports(NotificationChannel::SLACK));
        $this->assertFalse($this->notifier->supports(NotificationChannel::PUSH));
    }

    public function testGetChannelName(): void
    {
        $this->assertSame('email', $this->notifier->getChannelName());
    }

    public function testSendSuccessfully(): void
    {
        $recipient = 'test@example.com';
        $message = 'Test message';
        $metadata = ['subject' => 'Test Subject'];

        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($recipient, $message, $metadata) {
                $this->assertSame('from@example.com', $email->getFrom()[0]->getAddress());
                $this->assertSame($recipient, $email->getTo()[0]->getAddress());
                $this->assertSame($metadata['subject'], $email->getSubject());
                $this->assertSame($message, $email->getTextBody());

                return true;
            }));

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Email notification sent', [
                'channel' => 'email',
                'recipient' => $recipient,
                'subject' => $metadata['subject'],
            ]);

        $this->notifier->send($recipient, $message, $metadata);
    }

    public function testSendWithHtml(): void
    {
        $recipient = 'test@example.com';
        $message = 'Test message';
        $metadata = ['subject' => 'Test Subject', 'html' => '<p>HTML content</p>'];

        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($metadata) {
                $this->assertSame($metadata['html'], $email->getHtmlBody());

                return true;
            }));

        $this->notifier->send($recipient, $message, $metadata);
    }

    public function testSendThrowsExceptionOnFailure(): void
    {
        $recipient = 'test@example.com';
        $message = 'Test message';

        $this->mailer->expects($this->once())
            ->method('send')
            ->willThrowException(new TransportException('Failed to send'));

        $this->logger->expects($this->once())
            ->method('error')
            ->with('Failed to send email notification', [
                'recipient' => $recipient,
                'error' => 'Failed to send',
            ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to send email to test@example.com: Failed to send');

        $this->notifier->send($recipient, $message);
    }
}
