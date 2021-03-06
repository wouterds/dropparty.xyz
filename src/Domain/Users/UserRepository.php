<?php

namespace DropParty\Domain\Users;

interface UserRepository
{
    /**
     * @param User $user
     */
    public function add(User $user);

    /**
     * @param UserId $id
     * @return null|User
     */
    public function find(UserId $id): ?User;

    /**
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;
}
