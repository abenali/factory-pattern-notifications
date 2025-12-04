<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Notification;

interface NotificationRepositoryInterface
{
    /**
     * Save a notification.
     */
    public function save(Notification $notification): void;

    /**
     * Find a notification by ID.
     *
     * @throws \RuntimeException if notification not found
     */
    public function findById(string $id): Notification;
}
