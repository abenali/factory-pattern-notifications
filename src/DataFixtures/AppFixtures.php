<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Entity\User;
use App\Domain\ValueObject\NotificationChannel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ========== USER 1 : All channels available ==========
        $user1 = new User(
            email: 'all.channels@example.com',
            emailVerified: true,
            phone: '+33612345678',
            pushToken: 'push-token-abc123',
            slackUserId: 'U123ABC',
            preferredChannel: NotificationChannel::EMAIL
        );
        $manager->persist($user1);
        echo 'User 1 (All channels): '.$user1->getId()."\n";

        // ========== USER 2 : Only Email ==========
        $user2 = new User(
            email: 'email.only@example.com',
            emailVerified: true,
            phone: null,
            pushToken: null,
            slackUserId: null,
            preferredChannel: NotificationChannel::EMAIL
        );
        $manager->persist($user2);
        echo 'User 2 (Email only): '.$user2->getId()."\n";

        // ========== USER 3 : Only SMS ==========
        $user3 = new User(
            email: 'sms.only@example.com',
            emailVerified: false,
            phone: '+33698765432',
            pushToken: null,
            slackUserId: null,
            preferredChannel: NotificationChannel::SMS
        );
        $manager->persist($user3);
        echo 'User 3 (SMS only): '.$user3->getId()."\n";

        // ========== USER 4 : Only Push ==========
        $user4 = new User(
            email: 'push.only@example.com',
            emailVerified: false,
            phone: null,
            pushToken: 'push-token-xyz789',
            slackUserId: null,
            preferredChannel: NotificationChannel::PUSH
        );
        $manager->persist($user4);
        echo 'User 4 (Push only): '.$user4->getId()."\n";

        // ========== USER 5 : Only Slack ==========
        $user5 = new User(
            email: 'slack.only@example.com',
            emailVerified: false,
            phone: null,
            pushToken: null,
            slackUserId: 'U456DEF',
            preferredChannel: NotificationChannel::SLACK
        );
        $manager->persist($user5);
        echo 'User 5 (Slack only): '.$user5->getId()."\n";

        // ========== USER 6 : Prefers SMS but has Email fallback ==========
        $user6 = new User(
            email: 'sms.with.fallback@example.com',
            emailVerified: true,
            phone: '+33611223344',
            pushToken: null,
            slackUserId: null,
            preferredChannel: NotificationChannel::SMS
        );
        $manager->persist($user6);
        echo 'User 6 (SMS + Email fallback): '.$user6->getId()."\n";

        // ========== USER 7 : Prefers Push but only Email available ==========
        $user7 = new User(
            email: 'push.prefer.but.email@example.com',
            emailVerified: true,
            phone: null,
            pushToken: null, // Preferred Push not available
            slackUserId: null,
            preferredChannel: NotificationChannel::PUSH
        );
        $manager->persist($user7);
        echo 'User 7 (Prefers Push, fallback to Email): '.$user7->getId()."\n";

        // ========== USER 8 : NO channels available (for testing error) ==========
        $user8 = new User(
            email: 'no.channels@example.com',
            emailVerified: false,
            phone: null,
            pushToken: null,
            slackUserId: null,
            preferredChannel: NotificationChannel::EMAIL
        );
        $manager->persist($user8);
        echo 'User 8 (NO channels available): '.$user8->getId()."\n";

        $manager->flush();

        echo "\n=== FIXTURES LOADED ===\n";
        echo "8 users created with various channel configurations\n";
        echo "=======================\n\n";
    }
}
