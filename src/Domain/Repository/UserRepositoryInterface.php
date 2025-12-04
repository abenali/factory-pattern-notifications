<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\User;

interface UserRepositoryInterface
{
    /**
     * Find a user by ID.
     *
     * @throws \RuntimeException if user not found
     */
    public function findById(string $id): User;

    /**
     * Save a user.
     */
    public function save(User $user): void;
}
